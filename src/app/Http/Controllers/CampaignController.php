<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\Campaign;
use App\Models\CampaignRecipient;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
class CampaignController extends BaseController
{
    

public function store(Request $request)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'body' => 'required',
        'send_at' => 'required|date|after:now',
        'subscriber_ids' => 'required|array',
    ]);

    $campaign = DB::transaction(function () use ($request) {
        $campaign = Campaign::create([
            'title' => $request->title,
            'body' => $request->body,
            'send_at' => $request->send_at,
            'status' => 'scheduled',
            'created_by' => auth()->id(),
        ]);

        $recipients = [];
        foreach ($request->subscriber_ids as $subscriberId) {
            $recipients[] = [
                'campaign_id' => $campaign->id,
                'subscriber_id' => $subscriberId,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        CampaignRecipient::insert($recipients);
        return $campaign;
    });

    // --- PHẦN BỔ SUNG: Đẩy vào Redis Queue ---
    // Tính toán khoảng thời gian chờ (giây) từ bây giờ đến lúc gửi
    $delay = now()->diffInSeconds($campaign->send_at);

    // Dispatch Job và hẹn giờ (delay)
    \App\Jobs\SendCampaignJob::dispatch($campaign)
        ->onQueue('emails')
        ->delay($delay);

    return redirect()->route('campaigns.index')
        ->with('success', 'Chiến dịch đã được tạo và lên lịch gửi vào lúc ' . $campaign->send_at);
}
    
}
