<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Interfaces\SubscriberRepositoryInterface;
use App\Models\Subscriber;

class SubscriberRepository implements SubscriberRepositoryInterface
{
  /**
   * Get all subscribers
   * @return Collection
   */
  public function getAll()
  {
    return Subscriber::all();
  }

  /**
   * Get total subscribers
   * @return int
   */
  public function getTotal()
  {
    return Subscriber::count();
  }

  /**
   * Search subscribers by name or email
   * @param string $query
   * @param int $limit
   * @return \Illuminate\Database\Eloquent\Collection
   */
  public function search(string $query, int $limit = 20)
  {
    return Subscriber::where('name', 'like', "%{$query}%")
      ->orWhere('email', 'like', "%{$query}%")
      ->orderBy('name')
      ->limit($limit)
      ->get(['id', 'name', 'email']);
  }
}
