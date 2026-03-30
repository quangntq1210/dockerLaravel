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
    // 1. Validate dữ liệu đầu vào
    $request->validate([
        'title' => 'required|string|max:255',
        'body' => 'required',
        'send_at' => 'required|date|after:now',
        'subscriber_ids' => 'required|array',
    ]);

    // 2. Sử dụng Transaction để đảm bảo tính an toàn (Atomic)
    DB::transaction(function () use ($request) {
        // Tạo Campaign mới
        $campaign = Campaign::create([
            'title' => $request->title,
            'body' => $request->body,
            'send_at' => $request->send_at,
            'status' => 'scheduled',
            'created_by' => auth()->id(),
        ]);

        // Chuẩn bị dữ liệu để Insert hàng loạt vào bảng trung gian
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

        // Bulk Insert: Tối ưu hiệu năng, chỉ tốn 1 câu lệnh SQL duy nhất
        CampaignRecipient::insert($recipients);
    });

    return redirect()->route('campaigns.index')->with('success', 'Chiến dịch đã được tạo và lên lịch gửi!');
}
    
}
