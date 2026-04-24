<?php

namespace App\Http\Services;

use App\Repositories\Interfaces\CampaignRecipientsRepositoryInterface;
use App\Repositories\Interfaces\SubscriberRepositoryInterface;

class CampaignRecipientService
{
    protected $campaignRecipientsRepo;
    protected $subscriberRepo;
    
    /**
     * Constructor
     * @param CampaignRecipientsRepositoryInterface $campaignRecipientsRepo
     * @param SubscriberRepositoryInterface $subscriberRepo
     * @return void
     */
    public function __construct(CampaignRecipientsRepositoryInterface $campaignRecipientsRepo, SubscriberRepositoryInterface $subscriberRepo)
    {
        $this->campaignRecipientsRepo = $campaignRecipientsRepo;
        $this->subscriberRepo = $subscriberRepo;
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

        return $this->campaignRecipientsRepo->createCampaignRecipientsBulk($subscriberId, $campaigns);
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