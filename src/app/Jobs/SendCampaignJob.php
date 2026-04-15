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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SendCampaignJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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

        try {
            Mail::to($subscriber->email)->send(new CampaignMail($campaign, $subscriber));

            if (count(Mail::failures()) > 0) {
                throw new \RuntimeException('SMTP reported failures: ' . implode(',', Mail::failures()));
            }

            DB::transaction(function () use ($recipient, $campaign, $subscriber, $notifRepo, $recipientRepo, $campaignRepo) {
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
            });
        } catch (\Throwable $e) {
            Log::error('SendCampaignJob handle failed', [
                'campaign_id'   => $recipient->campaign_id,
                'subscriber_id' => $recipient->subscriber_id,
                'recipient_id'  => $recipient->id,
                'attempt'       => $this->attempts(),
                'error'         => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    public function failed(\Throwable $exception)
    {
        $recipientRepo = app(CampaignRecipientsRepositoryInterface::class);
        $campaignRepo  = app(CampaignRepositoryInterface::class);

        $recipient = $recipientRepo->getById($this->recipient->id);

        if (!$recipient) {
            return;
        }

        if ($recipient->status !== 'sent') {
            $recipientRepo->update(['status' => 'failed'], $recipient->id);
        }

        Log::error('SendCampaignJob failed', [
            'campaign_id'   => $recipient->campaign_id,
            'subscriber_id' => $recipient->subscriber_id,
            'recipient_id'  => $recipient->id,
            'attempt'       => $this->attempts(),
            'error'         => $exception->getMessage(),
        ]);

        if (!$recipientRepo->hasPending($recipient->campaign_id)) {
            $campaignRepo->update(['status' => 'sent'], $recipient->campaign_id);
        }
    }
}
