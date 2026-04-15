<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Services\CampaignRecipientService;
use App\Http\Requests\CampaignRecipientRequest;

class CampaignRecipientController extends ApiController
{

    protected $campaignRecipientService;

    /**
     * Constructor
     * @param CampaignRecipientService $campaignRecipientService
     */
    public function __construct(CampaignRecipientService $campaignRecipientService)
    {
        $this->campaignRecipientService = $campaignRecipientService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Create campaign recipients bulk
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeBulk(CampaignRecipientRequest $campaignRecipientRequest) {
        $userId = auth()->user()->id;
        $campaigns = $campaignRecipientRequest->input('campaigns');

        try {
            $this->campaignRecipientService->createCampaignRecipientsBulk($userId, $campaigns);

            return $this->success(__('message.campaign_recipients_created_successfully'), null, null, 201);
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
