<?php

namespace App\Http\Services;

use App\Repositories\Interfaces\CampaignRepositoryInterface;

class CampaignService
{
    protected $campaignRepo;

    /**
     * Constructor
     * @param CampaignRepositoryInterface $campaignRepo
     * @return void
     */
    public function __construct(CampaignRepositoryInterface $campaignRepo)
    {
        $this->campaignRepo = $campaignRepo;
    }

    /**
     * Get campaigns that are draft
     * @param $perPage
     * @param $page
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getDraftBySubscriberId($perPage = 20, $page = 1)
    {
        $user = auth()->user();
    
        $subscriberId = null;
    
        if ($user && $user->subscriber) {
            $subscriberId = $user->subscriber->id;
        }
    
        return $this->campaignRepo->getDraftBySubscriberId($subscriberId, $perPage, $page);
    }
}