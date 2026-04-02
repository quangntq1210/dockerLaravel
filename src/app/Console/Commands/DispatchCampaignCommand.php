<?php

namespace App\Console\Commands;

use App\Jobs\SendCampaignJob;
use App\Repositories\Interfaces\CampaignRecipientsRepositoryInterface;
use App\Repositories\Interfaces\CampaignRepositoryInterface;
use Illuminate\Console\Command;

class DispatchCampaignCommand extends Command
{
    protected $signature = 'campaigns:dispatch';

    protected $description = 'Dispatch jobs for campaigns that are scheduled and due to send';

    protected $campaignRepo;
    protected $recipientRepo;

    public function __construct(
        CampaignRepositoryInterface $campaignRepo,
        CampaignRecipientsRepositoryInterface $recipientRepo
    ) {
        parent::__construct();
        $this->campaignRepo  = $campaignRepo;
        $this->recipientRepo = $recipientRepo;
    }

    public function handle()
    {
        $campaigns = $this->campaignRepo->getScheduledDue();

        if ($campaigns->isEmpty()) {
            $this->info('No campaigns to dispatch.');
            return;
        }

        foreach ($campaigns as $campaign) {
            $this->campaignRepo->update(['status' => 'processing'], $campaign->id);

            $recipients = $this->recipientRepo->getPendingByCampaignId($campaign->id);

            foreach ($recipients as $recipient) {
                SendCampaignJob::dispatch($recipient);
            }

            $this->info("Dispatched {$recipients->count()} jobs for campaign [{$campaign->id}] \"{$campaign->title}\".");
        }
    }
}
