<?php

namespace App\Repositories\Interfaces;

interface CampaignRepositoryInterface
{
  /**
   * Create new campaign
   */
  public function create(array $data);

  /**
   * Update campaign by ID
   */
  public function update(array $data, $id);

  /**
   * Delete campaign by ID
   */
  public function delete($id);

  /**
   * Get campaign by ID
   */
  public function getById($id);

  /**
   * Get all campaigns
   */
  public function getAll();

  /**
   * Get campaigns by user ID
   */
  public function getByUserId($userId);

  /**
   * Get campaigns by user ID with pagination
   */
  public function getByUserIdWithPagination($userId, $perPage = 10, $page = 1);

  /**
   * Get campaigns that are scheduled and due to send (send_at <= now)
   */
  public function getScheduledDue();

  /**
   * Get campaigns that are draft and created_at descending
   * @return Collection
   */
  public function getDraftAndCreatedAtDescending();

  /**
   * Get total campaigns
   */
  public function getTotal();

  /**
   * Get total by status
   * @param string $status
   * @return int
   */
  public function getTotalByStatus($status);
}
