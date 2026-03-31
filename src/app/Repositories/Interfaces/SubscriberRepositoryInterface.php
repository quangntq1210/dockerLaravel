<?php

namespace App\Repositories\Interfaces;

interface SubscriberRepositoryInterface
{
  /**
   * Get all subscribers
   * @return Collection
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
}
