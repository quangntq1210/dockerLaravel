<?php

namespace App\Repositories\Interfaces;

interface SubscriberRepositoryInterface
{
  /**
   * Create new subscriber
   * @param array $data
   * @return \Illuminate\Database\Eloquent\Model
   */
  public function create(array $data);

  /**
   * Get all subscribers
   * @return \Illuminate\Database\Eloquent\Collection
   */
  public function getAll();

  /**
   * Get total subscribers
   * @return int
   */
  public function getTotal();

  /**
   * Search subscribers by name or email
   * @param string $query
   * @param int $limit
   * @return \Illuminate\Database\Eloquent\Collection
   */
  public function search(string $query, int $limit = 20);

  /**
   * Get subscriber by user ID
   * @param $userId
   * @return \App\Models\Subscriber | null
   */
  public function getByUserId($userId);

  /**
   * Get subscriber by email.
   * @param string $email
   * @return \App\Models\Subscriber | null
   */
  public function getByEmail(string $email);

  /**
   * First or create subscriber
   * @param array $data
   * @return \Illuminate\Database\Eloquent\Model
   */
  public function firstOrCreate(array $data);
}
