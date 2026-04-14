<?php

namespace App\Http\Controllers;

use App\Http\Services\AddNewCampaignService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\CreateCampaignRequest;
use Exception;

namespace App\Http\Controllers;


use App\Http\Services\AddNewCampaignService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\CreateCampaignRequest;
use Exception;

class AddNewCampaignController extends Controller
{
    protected $service;

    public function __construct(AddNewCampaignService $service)
    {
        $this->service = $service;
    }

    public function store(CreateCampaignRequest $request)
    {
        try {
           
            $campaign = $this->service->createCampaign($request->validated());

            return response()->json([
                'success' => true,
                'message' => __('message.save_success'),
                'data'    => $campaign
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'System error: ' . $e->getMessage()
            ], 500);
        }
    }
}