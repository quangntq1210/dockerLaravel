<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Services\HomeService;
use App\Http\Requests\HomeRequest;

class HomeController extends ApiController
{
    protected $homeService;

    public function __construct(HomeService $homeService)
    {
        $this->homeService = $homeService;
    }

    /**
     * Display the home/subscribe page.
     */
    public function index(Request $request)
    {
        if (auth()->check()) {
            $campaigns = $this->homeService->getCampaignsForAuth(12);
            $subscribedCampaignIds = $this->homeService->getSubscribedCampaignIdsByUserId(auth()->id());
        } else {
            $campaigns = $this->homeService->getCampaignsDraft(12);
            $subscribedCampaignIds = [];
        }

        return view('user.home', compact('campaigns', 'subscribedCampaignIds'));
    }

    /**
     * Handle public subscription form submission.
     */
    public function store(HomeRequest $homeRequest)
    {
        $validated = $homeRequest->validated();

        try {
            $this->homeService->createCampaignRecipientsBulk($validated);

            return $this->success(__('message.subscribe_success'), null, null, 201);
        } catch (\Throwable $th) {
            return $this->error(__('message.subscribe_failed'), 500);
        }
    }

    /**
     * Handle public unsubscribe form submission.
     * @param HomeRequest $homeRequest
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(HomeRequest $homeRequest)
    {
        $validated = $homeRequest->validated();

        try {
            $deleted = $this->homeService->deleteCampaignRecipients($validated);
            if ($deleted === 0) {
                return $this->error(__('message.error_occurred'), 404);
            }

            return $this->success(__('message.unsubscribe_success'), null, null, 200);
        } catch (\Throwable $th) {
            return $this->error(__('message.error_occurred'), 500);
        }
    }
}
