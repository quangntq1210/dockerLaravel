<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Interfaces\SubscriberRepositoryInterface;
use App\Models\Subscriber;

class SubscriberRepository implements SubscriberRepositoryInterface
{

  /**
   * Create new subscriber 
   * @param array $data
   * @return Subscriber
   */
  public function create(array $data)
  {
    return Subscriber::create($data);
  }

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

  /**
   * Check if subscriber exists by user ID
   * @param $userId
   * @return Subscriber | null
   */
  public function getByUserId($userId)
  {
    return Subscriber::where('user_id', $userId)->first();
  }

  /**
   * Get subscriber by email.
   * @param string $email
   * @return Subscriber | null
   */
  public function getByEmail(string $email)
  {
    return Subscriber::where('email', $email)->first();
  }

  /**
   * First or create subscriber
   * @param array $data
   * @return Subscriber
   */
  public function firstOrCreate(array $data)
  {
    return Subscriber::firstOrCreate($data);
  }
}
