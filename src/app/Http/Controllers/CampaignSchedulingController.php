<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CampaignSchedulingRequest;
use App\Repositories\Interfaces\CampaignRepositoryInterface;
use App\Repositories\Interfaces\CampaignRecipientsRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class CampaignSchedulingController extends Controller
{
    protected $campaignRepo;
    protected $campaignRecipientsRepo;

    public function __construct(
        CampaignRepositoryInterface $campaignRepo,
        CampaignRecipientsRepositoryInterface $campaignRecipientsRepo
    ) {
        $this->campaignRepo = $campaignRepo;
        $this->campaignRecipientsRepo = $campaignRecipientsRepo;
    }

    public function index()
    {
        $campaigns = $this->campaignRepo->getDraftAndCreatedAtDescending();

        return view('admin.campaign-scheduling', compact('campaigns'));
    }

    public function store(CampaignSchedulingRequest $request)
    {
        $data = $request->validated();

        try {
            DB::transaction(function () use ($data) {

                $this->campaignRepo->update([
                    'send_at' => $data['send_at'],
                    'status'  => 'scheduled',
                ], $data['campaign_id']);

                $this->campaignRecipientsRepo->deleteByCampaignId($data['campaign_id']);

                $recipients = array_map(fn($subscriberId) => [
                    'campaign_id'   => $data['campaign_id'],
                    'subscriber_id' => $subscriberId,
                    'status'        => 'pending',
                    'sent_at'       => null,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ], $data['subscriber_ids']);

                $this->campaignRecipientsRepo->createBulk($recipients);
            });

            Cache::forget('admin.dashboard.stats');

            return redirect()
                ->route('admin.campaign-scheduling')
                ->with('success', 'Đã lên lịch gửi thành công!');

        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->withErrors(['general' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
    }
}