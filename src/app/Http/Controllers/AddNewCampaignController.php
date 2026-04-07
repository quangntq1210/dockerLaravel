<?php

namespace App\Http\Controllers;

use App\Http\Services\AddNewCampaignService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Exception;

class AddNewCampaignController extends Controller
{
    protected $campaignService;

    public function __construct(AddNewCampaignService $campaignService)
    {
        $this->campaignService = $campaignService;
    }

    public function store(Request $request)
    {
        try {
        
            $validated = $request->validate([
                'title'   => 'required|max:255',
                'content' => 'required', 
            ]);

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