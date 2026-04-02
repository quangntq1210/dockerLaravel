<?php

namespace App\Jobs;

use App\Mail\CampaignMail;
use App\Models\CampaignRecipient;
use App\Repositories\Interfaces\CampaignRecipientsRepositoryInterface;
use App\Repositories\Interfaces\CampaignRepositoryInterface;
use App\Repositories\Interfaces\NotificationRepositoryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
class SendCampaignJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $recipient;
    public function __construct(CampaignRecipient $recipient)
    {
        $this->recipient = $recipient;
    }

public function handle(
    NotificationRepositoryInterface $notifRepo,
    CampaignRecipientsRepositoryInterface $recipientRepo,
    CampaignRepositoryInterface $campaignRepo
) {
    $recipient  = $this->recipient;
    $campaign   = $recipient->campaign;
    $subscriber = $recipient->subscriber;

    if (!$campaign || !$subscriber) {
        $recipientRepo->update(['status' => 'failed'], $recipient->id);
        return;
    }
    Mail::to($subscriber->email)
        ->send(new CampaignMail($campaign, $subscriber));

    DB::beginTransaction();

    try {
        if ($subscriber->user_id) {
            $notifRepo->create([
                'user_id'     => $subscriber->user_id,
                'campaign_id' => $campaign->id,
                'title'       => $campaign->title,
                'message'     => $campaign->body,
            ]);
        }

        $recipientRepo->update([
            'status'  => 'sent',
            'sent_at' => now(),
        ], $recipient->id);

        if (!$recipientRepo->hasPending($campaign->id)) {
            $campaignRepo->update(['status' => 'sent'], $campaign->id);
        }

        DB::commit();

    } catch (\Exception $e) {
        DB::rollBack();

        $recipientRepo->update(['status' => 'failed'], $recipient->id);

        throw $e; 
    }
}
}
