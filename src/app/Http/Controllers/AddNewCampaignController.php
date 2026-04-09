<?php

namespace App\Http\Controllers;

use App\Http\Services\AddNewCampaignService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\CreateCampaignRequest;
use Exception;

class AddNewCampaignController extends Controller
{
    protected $campaignService;

    public function __construct(AddNewCampaignService $campaignService)
    {
        $this->campaignService = $campaignService;
    }

    public function store(CreateCampaignRequest $request)
    {
        try {
        
           $validated = $request->validated();

            $result = $this->campaignService->createCampaign($validated);

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => __('message.save_success'), 
                    'data'    => $result
                ]);
            }
        } catch (Exception $e) {
           
            return response()->json([
                'success' => false,
                'message' => $e->getMessage() 
            ], 500);
        }

        return response()->json([
            'success' => false,
            'message' => __('message.save_error')
        ], 500);
    }
}