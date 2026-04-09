<?php

namespace App\Http\Services;

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
}