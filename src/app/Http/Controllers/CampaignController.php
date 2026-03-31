<?php

namespace App\Http\Controllers;

use App\Http\Requests\CampaignRequest;
use App\Repositories\Interfaces\CampaignRepositoryInterface;
use App\Http\Controllers\ApiController;

class CampaignController extends ApiController
{
    protected $campaignRepo;

    /**
     * Constructor
     * @param CampaignRepositoryInterface $campaignRepo
     */
    public function __construct(CampaignRepositoryInterface $campaignRepo)
    {
        $this->campaignRepo = $campaignRepo;
    }

    /**
     * Create a new campaign
     * @param CampaignRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CampaignRequest $request)
    {
        $data = $request->validated();
        $campaign = $this->campaignRepo->create($data);
        if (!$campaign) {
            return $this->error('Failed to create campaign', 400);
        }
        return $this->success('Campaign created successfully', $campaign, ['campaign' => $campaign->toArray()], 201);
    }
}
