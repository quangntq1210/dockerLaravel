<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Interfaces\CampaignRecipientsRepositoryInterface;
use App\Models\CampaignRecipient;

class CampaignRecipientsRepository implements CampaignRecipientsRepositoryInterface
{

  /*
  * Create new campaign recipient
  * @param array $data
  * @return CampaignRecipient
  */
  public function create(array $data)
  {
    return CampaignRecipient::create($data);
  }

  /*
  * Create new campaign recipients
  * @param array $data
  * @return bool
  */
  public function createBulk(array $data)
  {
    return CampaignRecipient::insert($data);
  }

  /*
  * Update campaign recipient by ID
  * @param array $data
  * @param int $id
  * @return CampaignRecipient
  */
  public function update(array $data, $id)
  {
    return CampaignRecipient::where('id', $id)->update($data);
  }

  /*
  * Delete campaign recipient by ID
  * @param int $id
  * @return bool
  */
  public function delete($id)
  {
    return CampaignRecipient::where('id', $id)->delete();
  }

  /*
  * Delete campaign recipients by campaign ID
  * @param int $campaignId
  * @return bool
  */
  public function deleteByCampaignId($campaignId)
  {
    return CampaignRecipient::where('campaign_id', $campaignId)->delete();
  }


  /*
  * Get campaign recipient by ID
  * @param int $id
  * @return CampaignRecipient
  */
  public function getById($id)
  {
    return CampaignRecipient::find($id);
  }

  
  /*
  * Get all campaign recipients
  * @return Collection
  */
  public function getAll()
  {
    return CampaignRecipient::all();
  }


  /*
  * Get campaign recipients by user ID
  * @param int $userId
  * @return Collection
  */
  public function getByUserId($userId)
  {
    return CampaignRecipient::where('user_id', $userId)->get();
  }


  /*
  * Get campaign recipients by user ID with pagination
  * @param int $userId
  * @param int $perPage
  * @param int $page
  * @return Collection
  */
  public function getByUserIdWithPagination($userId, $perPage = 10, $page = 1)
  {
    return CampaignRecipient::where('user_id', $userId)->paginate($perPage, ['*'], 'page', $page);
  }


  /*
  * Get pending recipients by campaign ID
  * @param int $campaignId
  * @return Collection
  */
  public function getPendingByCampaignId($campaignId)
  {
    return CampaignRecipient::where('campaign_id', $campaignId)
      ->where('status', 'pending')
      ->get();
  }


  /*
  * Check if campaign still has pending recipients
  * @param int $campaignId
  * @return bool
  */
  public function hasPending($campaignId)
  {
    return CampaignRecipient::where('campaign_id', $campaignId)
      ->where('status', 'pending')
      ->exists();
  }

  /**
   * Claim pending recipient
   * @param int $recipientId
   * @return bool
   */
  public function claimPendingRecipient(int $recipientId): bool
  {
    return CampaignRecipient::where('id', $recipientId)
      ->where('status', 'pending')
      ->update(['status' => 'processing']) === 1;
  }

  /**
   * Create campaign recipients bulk
   * @param $userId
   * @param $campaigns
   * @return void
   */
  public function createCampaignRecipientsBulk($userId, $campaigns)
  {
    CampaignRecipient::insert(array_map(fn($campaignId) => [
      'campaign_id' => $campaignId,
      'subscriber_id' => $userId,
      'status' => 'draft',
      'sent_at' => null,
      'created_at' => now(),
      'updated_at' => now(),
    ], $campaigns));
  }

  /**
   * Check if campaign recipient exists by campaign ID and subscriber ID
   * @param int $campaignId
   * @param int $subscriberId
   * @return bool
   */
  public function existsByCampaignIdAndSubscriberId(int $campaignId, int $subscriberId): bool
  {
    return CampaignRecipient::where('campaign_id', $campaignId)
      ->where('subscriber_id', $subscriberId)
      ->exists();
  }

  /**
   * Get campaign recipients by campaign ID and subscriber IDs
   * @param int $campaignId
   * @param array $subscriberIds
   * @return \Illuminate\Support\Collection
   */
  public function getByCampaignIdAndSubscriberIds(int $campaignId, array $subscriberIds)
  {
    return CampaignRecipient::where('campaign_id', $campaignId)
      ->whereIn('subscriber_id', $subscriberIds)
      ->get();
  }

  /**
   * Update campaign recipients status by campaign ID and subscriber IDs
   * @param int $campaignId
   * @param array $subscriberIds
   * @param string $fromStatus
   * @param string $toStatus
   * @return int
   */
  public function updateStatusByCampaignIdAndSubscriberIds(int $campaignId, array $subscriberIds, string $fromStatus, string $toStatus) : int
  {
    return CampaignRecipient::where('campaign_id', $campaignId)
      ->whereIn('subscriber_id', $subscriberIds)
      ->where('status', $fromStatus)
      ->update(['status' => $toStatus]);
  }

  /**
   * Delete campaign recipient by campaign id and subscriber id.
   * @param int $campaignId
   * @param int $subscriberId
   * @return int
   */
  public function deleteByCampaignIdAndSubscriberId(int $campaignId, int $subscriberId): int
  {
    return CampaignRecipient::where('campaign_id', $campaignId)
      ->where('subscriber_id', $subscriberId)
      ->delete();
  }
}
