<?php

namespace App\Http\Controllers;

use App\Http\Requests\CampaignRequest;
use App\Http\Controllers\ApiController;
use App\Http\Services\CampaignService;
use Illuminate\Http\Request;

class CampaignController extends ApiController
{
    protected $campaignRepo;
    protected $campaignService;

    /**
     * Constructor
     * @param CampaignService $campaignService
     */
    public function __construct(CampaignService $campaignService)   
    {
        $this->campaignService = $campaignService;
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

    /**
     * Get campaigns that are draft and 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCampaignsDraft(Request $request) {
        $perPage = $request->get('per_page', 20);
        $page = $request->get('page', 1);

        $campaigns = $this->campaignService->getDraftBySubscriberId($perPage, $page);

        return $this->success(
            "Campaigns fetched successfully",
            $campaigns->items(),
            [
                'current_page' => $campaigns->currentPage(),
                'last_page'    => $campaigns->lastPage(),
                'per_page'     => $campaigns->perPage(),
                'total'        => $campaigns->total(),
            ],
            200
        );
    }
}
