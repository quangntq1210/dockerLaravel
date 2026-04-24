<?php

namespace App\Http\Services;

use App\Models\Campaign;
use App\Repositories\Interfaces\CampaignRepositoryInterface;
use App\Repositories\Interfaces\SubscriberRepositoryInterface;
use App\Repositories\Interfaces\CampaignRecipientsRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Interfaces\NotificationRepositoryInterface;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HomeService
{
    protected $campaignRepo;
    protected $subscriberRepo;
    protected $campaignRecipientsRepo;
    protected $userRepo;
    protected $notificationRepo;
    /**
     * Constructor
     * @param CampaignRepositoryInterface $campaignRepo
     * @param SubscriberRepositoryInterface $subscriberRepo
     * @param CampaignRecipientsRepositoryInterface $campaignRecipientsRepo
     * @param UserRepositoryInterface $userRepo
     * @param NotificationRepositoryInterface $notificationRepo
     * @return void
     */
    public function __construct(CampaignRepositoryInterface $campaignRepo, SubscriberRepositoryInterface $subscriberRepo, CampaignRecipientsRepositoryInterface $campaignRecipientsRepo, UserRepositoryInterface $userRepo, NotificationRepositoryInterface $notificationRepo)
    {
        $this->campaignRepo = $campaignRepo;
        $this->subscriberRepo = $subscriberRepo;
        $this->campaignRecipientsRepo = $campaignRecipientsRepo;
        $this->userRepo = $userRepo;
        $this->notificationRepo = $notificationRepo;
    }

    /**
     * Get campaigns that are draft and created_at descending
     * @param int $perPage
     * @param int $page
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getCampaignsDraft($perPage = 10, $page = 1)
    {
        return $this->campaignRepo->getDraftAndCreatedAtDescending($perPage, $page);
    }

    /**
     * Get campaigns for home page (draft + scheduled).
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getCampaignsForHome(int $perPage = 12)
    {
        return Campaign::query()
            ->whereIn('status', ['draft', 'scheduled'])
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * Get campaigns for guest users.
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getCampaignsForGuest(int $perPage = 12)
    {
        return Campaign::query()
            ->where('status', 'draft')
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * Get campaigns for authenticated users.
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getCampaignsForAuth(int $perPage = 12)
    {
        return Campaign::query()
            ->whereIn('status', ['draft', 'scheduled', 'sent'])
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * Get subscribed campaign ids of current user.
     * @param int|null $userId
     * @return array<int>
     */
    public function getSubscribedCampaignIdsByUserId(?int $userId): array
    {
        if (!$userId) {
            return [];
        }

        $subscriber = $this->subscriberRepo->getByUserId($userId);
        if (!$subscriber) {
            return [];
        }

        return $subscriber->campaigns()->pluck('campaign_id')->toArray();
    }
    

    /**
     * Create campaign recipients bulk
     * @param array $data
     * @return void
     */
    public function createCampaignRecipientsBulk($data)
    {
        $shouldSendVerification = false;
        $user = null;

        try {
            DB::transaction(function () use ($data, &$shouldSendVerification, &$user) {
                $user = $this->userRepo->firstOrCreate([
                    'email' => $data['email'],
                    'name' => $data['name'],
                    'password' => Hash::make('12345678'),
                    'role' => 'user',
                ]);
        
                $subscriber = $this->subscriberRepo->firstOrCreate([
                    'email' => $data['email'],
                    'name' => $data['name'],
                    'user_id' => $user->id,
                ]);
        
                $now = now();
                $rows = [];
                $notiRows = [];
        
                foreach ($data['campaign_ids'] as $campaignId) {
                    $alreadyExists = $this->campaignRecipientsRepo->existsByCampaignIdAndSubscriberId($campaignId, $subscriber->id);
        
                    if (!$alreadyExists) {
                        $rows[] = [
                            'campaign_id'   => $campaignId,
                            'subscriber_id' => $subscriber->id,
                            'status'        => 'draft',
                            'sent_at'       => null,
                            'created_at'    => $now,
                            'updated_at'    => $now,
                        ];
                    }

                    $notiRows[] = [
                        'campaign_id' => $campaignId,
                        'title' => 'You have been subscribed to a new campaign',
                        'message' => 'You have been subscribed to a new campaign. Please check it out.',
                        'user_id' => $user->id,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
        
                if (!empty($rows)) {
                    $this->campaignRecipientsRepo->createBulk($rows);
                }

                if (!empty($notiRows)) {
                    $this->notificationRepo->createBulk($notiRows);
                }

                if (!$user->hasVerifiedEmail()) {
                    $shouldSendVerification = true;
                }
            });

            if ($shouldSendVerification) {
                event(new Registered($user));
            }
        } catch (\Throwable $th) {
            Log::error('HomeService createCampaignRecipientsBulk failed', [
                'message' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
                'data' => $data,
            ]);
            throw $th;
        }
    }

    /**
     * Unsubscribe guest/user by provided subscriber information.
     * @param array $data
     * @return int
     */
    public function unsubscribeCampaignRecipientsBulk(array $data): int
    {
        $subscriber = $this->subscriberRepo->getByEmail($data['email']);
        if (!$subscriber) {
            return 0;
        }

        if (strcasecmp((string) $subscriber->name, (string) $data['name']) !== 0) {
            return 0;
        }

        $deleted = 0;
        foreach ($data['campaign_ids'] as $campaignId) {
            $deleted += $this->campaignRecipientsRepo->deleteByCampaignIdAndSubscriberId((int) $campaignId, (int) $subscriber->id);
        }

        return $deleted;
    }


    /**
     * Delete campaign recipients by provided subscriber information.
     * @param array $data
     * @return int
     */
    public function deleteCampaignRecipients(array $data): int
    {
        $subscriber = $this->subscriberRepo->getByEmail($data['email']);
        if (!$subscriber) {
            return 0;
        }

        return $this->campaignRecipientsRepo->deleteByCampaignIdAndSubscriberId($data['campaign_id'], $subscriber->id);
    }
}