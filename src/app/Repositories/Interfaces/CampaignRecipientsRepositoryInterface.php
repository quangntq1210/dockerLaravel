<?php

namespace App\Repositories\Interfaces;

interface CampaignRecipientsRepositoryInterface
{
  /**
   * Create new campaign recipient
   */
  public function create(array $data);

  /**
   * Create new campaign recipients
   */
  public function createBulk(array $data);

  /**
   * Update campaign recipient by ID
   */

  public function update(array $data, $id);
  /**
   * Delete campaign recipient by ID
   */

  /**
   * Delete campaign recipient by ID
   */
  public function delete($id);

  /**
   * Delete campaign recipients by campaign ID
   */
  public function deleteByCampaignId($campaignId);

  /**
   * Get campaign recipient by ID
   */
  public function getById($id);

  /**
   * Get all campaign recipients
   */
  public function getAll();

  /**
   * Get campaign recipients by user ID
   */
  public function getByUserId($userId);

  /**
   * Get campaign recipients by user ID with pagination
   */
  public function getByUserIdWithPagination($userId, $perPage = 10, $page = 1);

  /**
   * Get pending recipients by campaign ID
   */
  public function getPendingByCampaignId($campaignId);

  /**
   * Check if campaign still has pending recipients
   */
  public function hasPending($campaignId);

  /**
   * Claim pending recipient
   * @param int $recipientId
   * @return bool
   */
  public function claimPendingRecipient(int $recipientId): bool;

  /**
   * Create campaign recipients bulk
   * @param $userId
   * @param $campaigns
   * @return void
   */
  public function createCampaignRecipientsBulk($userId, $campaigns);

  /**
   * Check if campaign recipient exists by campaign ID and subscriber ID
   * @param int $campaignId
   * @param int $subscriberId
   * @return bool
   */
  public function existsByCampaignIdAndSubscriberId(int $campaignId, int $subscriberId): bool;

  /**
   * Get campaign recipients by campaign ID and subscriber IDs
   * @param int $campaignId
   * @param array $subscriberIds
   * @return \Illuminate\Support\Collection
   */
  public function getByCampaignIdAndSubscriberIds(int $campaignId, array $subscriberIds);

  /**
   * Update campaign recipients status by campaign ID and subscriber IDs
   * @param int $campaignId
   * @param array $subscriberIds
   * @param string $fromStatus
   * @param string $toStatus
   * @return int
   */
  public function updateStatusByCampaignIdAndSubscriberIds(int $campaignId, array $subscriberIds, string $fromStatus, string $toStatus) : int;

  /**
   * Delete campaign recipient by campaign id and subscriber id.
   * @param int $campaignId
   * @param int $subscriberId
   * @return int
   */
  public function deleteByCampaignIdAndSubscriberId(int $campaignId, int $subscriberId): int;
}
