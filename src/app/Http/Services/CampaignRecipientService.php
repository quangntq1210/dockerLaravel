<?php

namespace App\Http\Services;

use App\Repositories\Interfaces\CampaignRecipientsRepositoryInterface;
use App\Repositories\Interfaces\SubscriberRepositoryInterface;
use App\Repositories\Interfaces\NotificationRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class CampaignRecipientService
{
    protected $campaignRecipientsRepo;
    protected $subscriberRepo;
    protected $notificationRepo;
    /**
     * Constructor
     * @param CampaignRecipientsRepositoryInterface $campaignRecipientsRepo
     * @param SubscriberRepositoryInterface $subscriberRepo
     * @param NotificationRepositoryInterface $notificationRepo
     * @return void
     */
    public function __construct(CampaignRecipientsRepositoryInterface $campaignRecipientsRepo, SubscriberRepositoryInterface $subscriberRepo, NotificationRepositoryInterface $notificationRepo)
    {
        $this->campaignRecipientsRepo = $campaignRecipientsRepo;
        $this->subscriberRepo = $subscriberRepo;
        $this->notificationRepo = $notificationRepo;
    }

    /**
     * Create campaign recipients bulk
     * @param $userId
     * @param $campaigns
     * @return void
     */
    public function createCampaignRecipientsBulk($userId, $campaigns)
    {
        $subscriber = $this->subscriberRepo->getByUserId($userId);

        if (!$subscriber) {
            $subscriber = $this->subscriberRepo->create([
                'name' => auth()->user()->name,
                'email' => auth()->user()->email,
                'user_id' => $userId
            ]);
        }

        $subscriberId = $subscriber->id;

        try {
            DB::transaction(function () use ($subscriberId, $campaigns, $userId) {
                $this->campaignRecipientsRepo->createCampaignRecipientsBulk($subscriberId, $campaigns);
                $this->notificationRepo->create([
                    'user_id' => $userId,
                    'campaign_id' => $campaigns[0],
                    'title' => 'Campaign Subscribed',
                    'message' => 'You have subscribed to the campaign',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });
        } catch (\Throwable $th) {
            Log::error('CampaignRecipientService createCampaignRecipientsBulk failed', [
                'message' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
                'campaigns' => $campaigns,
                'subscriber_id' => $subscriberId,
                'user_id' => $userId,
            ]);
            throw $th;
        }
    }

    /**
     * Unsubscribe current user from campaign.
     * @param int $userId
     * @param int $campaignId
     * @return int
     */
    public function unsubscribeFromCampaign(int $userId, int $campaignId): int
    {
        $subscriber = $this->subscriberRepo->getByUserId($userId);
        if (!$subscriber) {
            return 0;
        }

        return $this->campaignRecipientsRepo->deleteByCampaignIdAndSubscriberId($campaignId, (int) $subscriber->id);
    }
}