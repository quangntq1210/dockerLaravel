<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CampaignSchedulingRequest;
use App\Http\Services\CampaignSchedulingService;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CampaignSchedulingController extends Controller
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

            Cache::tags(['dashboard'])->flush();

            return redirect()
                ->route('admin.campaign-scheduling')
                ->with('success', __('message.campaign_scheduled'));
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->withErrors(['general' => __('message.error_occurred')]);
        }
    }
}