<?php

namespace App\Repositories\Interfaces;

interface CampaignRecipientsRepositoryInterface
{
  /**
   * Create new campaign recipient
   */
  public function create(array $data);
  /**
   * Update campaign recipient by ID
   */
  public function update(array $data, $id);
  /**
   * Delete campaign recipient by ID
   */
  public function delete($id);

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
}
