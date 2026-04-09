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
        $campaigns = $this->homeService->getCampaignsDraft(100, 1);

        return view('user.home', compact('campaigns'));
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
}
