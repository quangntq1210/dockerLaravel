<?php

namespace App\Jobs;

use App\Models\Campaign;
use App\Models\Subscriber;
use App\Models\CampaignRecipient;
use App\Models\Notification;
use App\Mail\CampaignMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendCampaignJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $campaign;

    public function __construct(Campaign $campaign)
    {
        $this->campaign = $campaign;
    }

    
  public function handle()
    {
        // 1. Cập nhật trạng thái campaign sang 'sending'
        $this->campaign->update(['status' => 'sending']);

        // 2. Lấy danh sách recipients
        $recipients = $this->campaign->recipients()->with('subscriber')->get();

        foreach ($recipients as $recipient) {
            try {
                $subscriber = $recipient->subscriber;

                // GỬI EMAIL (Đã bỏ dấu \ vì đã có 'use' ở đầu file)
                Mail::to($subscriber->email)->send(new CampaignMail($this->campaign));

                // Cập nhật trạng thái thành công
                $recipient->update([
                    'status' => 'sent',
                    'sent_at' => now()
                ]);

                // TẠO THÔNG BÁO (Đã bỏ dấu \ vì đã có 'use' ở đầu file)
                if ($subscriber->user_id) {
                    Notification::create([
                        'user_id' => $subscriber->user_id,
                        'campaign_id' => $this->campaign->id,
                        'title' => $this->campaign->title,
                        'message' => "Thông báo mới: " . $this->campaign->title,
                    ]);
                }
            } catch (\Exception $e) {
                $recipient->update(['status' => 'failed']);
            }
        }

        // 3. Hoàn tất
        $this->campaign->update(['status' => 'completed']);
    }
}