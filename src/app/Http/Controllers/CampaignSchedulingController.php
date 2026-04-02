<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CampaignSchedulingRequest;
use App\Http\Controllers\ApiController;
use App\Repositories\Interfaces\CampaignRepositoryInterface;
use App\Repositories\Interfaces\CampaignRecipientsRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class CampaignSchedulingController extends ApiController
{
    protected $campaignRepo;
    protected $campaignRecipientsRepo;
    /**
     * Constructor
     * @param CampaignRepositoryInterface $campaignRepo
     * @param CampaignRecipientsRepositoryInterface $campaignRecipientsRepo
     */
    public function __construct(CampaignRepositoryInterface $campaignRepo, CampaignRecipientsRepositoryInterface $campaignRecipientsRepo)
    {
        $this->campaignRepo = $campaignRepo;
        $this->campaignRecipientsRepo = $campaignRecipientsRepo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $campaigns = $this->campaignRepo->getDraftAndCreatedAtDescending();

        if ($campaigns->isEmpty()) {
            $message = 'Không có chiến dịch nào ở trạng thái nháp.';
            return view('admin.campaign-scheduling', compact('message', 'campaigns'));
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CampaignSchedulingRequest $request)
    {
        $data = $request->validated();

        try {
            DB::transaction(function () use ($data) {

                // Update campaign status and send_at
                $this->campaignRepo->update([
                    'send_at' => $data['send_at'],
                    'status'  => 'scheduled',
                ], $data['campaign_id']);

                // Delete old recipients to avoid duplicates
                $this->campaignRecipientsRepo->deleteByCampaignId($data['campaign_id']);
                $recipients = array_map(fn($subscriberId) => [
                    'campaign_id'   => $data['campaign_id'],
                    'subscriber_id' => $subscriberId,
                    'status'        => 'pending',
                    'sent_at'       => null,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ], $data['subscriber_ids']);

                $this->campaignRecipientsRepo->createBulk($recipients); // bulk insert efficiently
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
