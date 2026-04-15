<?php

namespace App\Http\Services;

use App\Repositories\Eloquent\CampaignRecipientsRepository;
use App\Repositories\Eloquent\CampaignRepository;
use App\Exceptions\ResourceNotFoundException;
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
              $campaignId = (int) $data['campaign_id'];
              $sendAt = $data['send_at'];

              $this->campaignRepo->update([
                  'send_at' => $sendAt,
                  'status'  => 'scheduled',
              ], $campaignId);

              $existing = $this->campaignRecipientsRepo
                  ->getByCampaignIdAndSubscriberIds($campaignId, $data['subscriber_ids']);

              $existingStatusBySubscriberId = [];
              foreach ($existing as $row) {
                  $existingStatusBySubscriberId[(int) $row->subscriber_id] = $row->status;
              }

              $now = now();
              $toInsert = [];
              $toPromoteDraftIds = [];

              foreach ($data['subscriber_ids'] as $subscriberId) {
                  if (!array_key_exists($subscriberId, $existingStatusBySubscriberId)) {
                      $toInsert[] = [
                          'campaign_id'   => $campaignId,
                          'subscriber_id' => $subscriberId,
                          'status'        => 'pending',
                          'sent_at'       => null,
                          'created_at'    => $now,
                          'updated_at'    => $now,
                      ];
                      continue;
                  }

                  if ($existingStatusBySubscriberId[$subscriberId] === 'draft') {
                      $toPromoteDraftIds[] = $subscriberId;
                  }
              }

              if (!empty($toInsert)) {
                  $this->campaignRecipientsRepo->createBulk($toInsert);
              }

              if (!empty($toPromoteDraftIds)) {
                  $updated = $this->campaignRecipientsRepo->updateStatusByCampaignIdAndSubscriberIds(
                      $campaignId,
                      $toPromoteDraftIds,
                      'draft',
                      'pending'
                  );

                  if ($updated === 0) {
                      throw new \Exception('Failed to promote draft recipients to pending');
                  }
              }
          });
      } catch (\Throwable $th) {
          Log::error('CampaignSchedulingService createCampaignScheduling failed', [
              'message' => $th->getMessage(),
              'campaign_id' => $data['campaign_id'] ?? null,
          ]);
          throw $th;
      }
  }
}
