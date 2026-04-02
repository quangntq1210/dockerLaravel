<?php

namespace App\Http\Services;

use App\Repositories\Eloquent\CampaignRecipientsRepository;
use App\Repositories\Eloquent\CampaignRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CampaignSchedulingService
{
  protected $campaignRepo;
  protected $campaignRecipientsRepo;

  /**
   * Constructor
   * @param CampaignRepository $campaignRepo
   * @param CampaignRecipientsRepository $campaignRecipientsRepo
   * @return void
   */
  public function __construct(CampaignRepository $campaignRepo, CampaignRecipientsRepository $campaignRecipientsRepo)
  {
    $this->campaignRepo = $campaignRepo;
    $this->campaignRecipientsRepo = $campaignRecipientsRepo;
  }

  /**
   * Get campaigns that are draft and created_at descending
   * @return \Illuminate\Support\Collection
   */
  public function getDraftAndCreatedAtDescending()
  {
    return $this->campaignRepo->getDraftAndCreatedAtDescending();
  }

  /**
   * Create a new campaign scheduling
   * @param array $data
   * @return void
   * @throws \Throwable
   */
  public function createCampaignScheduling($data)
  {
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
    } catch (\Throwable $th) {
      DB::rollBack();
      Log::error($th->getMessage());
      throw $th;
    }
  }
}
