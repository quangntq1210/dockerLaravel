<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CampaignSchedulingRequest;
use App\Http\Services\CampaignSchedulingService;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CampaignSchedulingController extends ApiController
{
    protected $campaignSchedulingService;

    /**
     * Constructor
     * @param CampaignSchedulingService $campaignSchedulingService
     */
    public function __construct(CampaignSchedulingService $campaignSchedulingService)
    {
        $this->campaignSchedulingService = $campaignSchedulingService;
    }

    /**
     * Display a listing of the campaigns that are draft and created_at descending.
     * @return \Illuminate\Contracts\View\View
     * @throws \Throwable
     */
    public function index()
    {
        try {
            $campaigns = $this->campaignSchedulingService->getDraftAndCreatedAtDescending();
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            abort(500);
        }
        return view('admin.campaign-scheduling', compact('campaigns'));
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
     * Create a new campaign scheduling
     * @param CampaignSchedulingRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Throwable
     */
    public function store(CampaignSchedulingRequest $request)
    {
        $data = $request->validated();

        try {
            $this->campaignSchedulingService->createCampaignScheduling($data);

            Cache::forget('admin.dashboard.stats');

            return redirect()
                ->route('admin.campaign-scheduling')
                ->with('success', __('message.campaign_scheduled'));
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->withErrors(['general' => __('message.error_occurred')]);
        }
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
