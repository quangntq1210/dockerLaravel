<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Models\User;

class UserRepository implements UserRepositoryInterface
{

  /**
   * Create new users 
   * @param array $data
   * @return User
   */
  public function create(array $data)
  {
    return User::create($data);
  }

  /**
   * Get all users
   * @return Collection
   */
  public function getAll()
  {
    return User::all();
  }

  /**
   * Get total users
   * @return int
   */
  public function getTotal()
  {
    return User::count();
  }

  /**
   * Search users by name or email
   * @param string $query
   * @param int $limit
   * @return \Illuminate\Database\Eloquent\Collection
   */
  public function search(string $query, int $limit = 20)
  {
    return User::where('name', 'like', "%{$query}%")
      ->orWhere('email', 'like', "%{$query}%")
      ->orderBy('name')
      ->limit($limit)
      ->get(['id', 'name', 'email']);
  }

  /**
   * Check if users exists by user ID
   * @param $userId
   * @return User | null
   */
  public function getByUserId($userId)
  {
    return User::where('user_id', $userId)->first();
  }

  /**
   * First or create users
   * @param array $data
   * @return User
   */
  public function firstOrCreate(array $data)
  {
    return User::firstOrCreate($data);
  }
}
