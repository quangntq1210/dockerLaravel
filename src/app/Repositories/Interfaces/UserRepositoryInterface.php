<?php

namespace App\Repositories\Interfaces;
use App\Models\User;

interface UserRepositoryInterface
{
  /**
   * Create new user
   * @param array $data
   * @return \Illuminate\Database\Eloquent\Model
   */
  public function create(array $data);

  /**
   * Get all user
   * @return \Illuminate\Database\Eloquent\Collection
   */
  public function getAll();

  /**
   * Get total user
   * @return int
   */
  public function getTotal();

  /**
   * Search user by name or email
   * @param string $query
   * @param int $limit
   * @return \Illuminate\Database\Eloquent\Collection
   */
  public function search(string $query, int $limit = 20);

  /**
   * Get user by user ID
   * @param $userId
   * @return \App\Models\User | null
   */
  public function getByUserId($userId);

  /**
   * First or create user
   * @param array $data
   * @return \App\Models\User
   */
  public function firstOrCreate(array $data);

  public function updatePassword(User $user, string $newPassword): bool;
}
