<?php

namespace App\Http\Services;

use App\Repositories\Interfaces\CampaignRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\Log;

class AddNewCampaignService
{
    protected $campaignRepository;

    
    public function __construct(CampaignRepositoryInterface $campaignRepository)
    {
        $this->campaignRepository = $campaignRepository;
    }

    public function createCampaign(array $data)
    {
        try {
         
            return $this->campaignRepository->create($data);
        } catch (Exception $e) {
            Log::error("Business Logic Error - Create Campaign: " . $e->getMessage());
            throw $e;
        }
    }
}