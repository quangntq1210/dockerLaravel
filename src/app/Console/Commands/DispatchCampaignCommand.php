<?php

namespace App\Console\Commands;

use App\Jobs\SendCampaignJob;
use App\Repositories\Interfaces\CampaignRecipientsRepositoryInterface;
use App\Repositories\Interfaces\CampaignRepositoryInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DispatchCampaignCommand extends Command
{
    protected $signature = 'campaigns:dispatch';

    protected $description = 'Dispatch jobs for campaigns that are scheduled and due to send';

    protected $campaignRepo;
    protected $recipientRepo;

    /**
     * Create a new command instance.
     *
     * @param CampaignRepositoryInterface $campaignRepo
     * @param CampaignRecipientsRepositoryInterface $recipientRepo
     * @return void
     */
    public function __construct(
        CampaignRepositoryInterface $campaignRepo,
        CampaignRecipientsRepositoryInterface $recipientRepo
    ) {
        parent::__construct();
        $this->campaignRepo  = $campaignRepo;
        $this->recipientRepo = $recipientRepo;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $campaigns = $this->campaignRepo->getScheduledDue();

        if ($campaigns->isEmpty()) {
            $this->info('No campaigns to dispatch.');
            return;
        }

        foreach ($campaigns as $campaign) {
            DB::transaction(function () use ($campaign) {
                if (!$this->campaignRepo->claimScheduledCampaign($campaign->id)) {
                    return;
                }

                $recipients = $this->recipientRepo->getPendingByCampaignId($campaign->id);

                $dispatched = 0;
                foreach ($recipients as $recipient) {
                    if (!$this->recipientRepo->claimPendingRecipient($recipient->id)) {
                        continue;
                    }

                    SendCampaignJob::dispatch($recipient);
                    $dispatched++;
                }

                $this->info("Dispatched {$dispatched} jobs for campaign [{$campaign->id}] \"{$campaign->title}\".");
            });
        }

        Cache::forget('admin.dashboard.stats');
    }
}
