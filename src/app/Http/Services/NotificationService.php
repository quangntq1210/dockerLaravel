<?php

namespace App\Http\Services;

use App\Repositories\Eloquent\NotificationRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class NotificationService
{
  public $notificationRepository;

  /**
   * Constructor
   * @param NotificationRepository $notificationRepository
   * @return void
   */
  public function __construct(NotificationRepository $notificationRepository)
  {
    $this->notificationRepository = $notificationRepository;
  }

  /**
   * Delete a notification
   * @param int $id
   * @return void
   */
  public function delete($id)
  {
    $this->notificationRepository->delete($id);
  }

  /**
   * List notifications for user
   * @param int $userId
   * @param int $perPage
   * @param int $page
   * @param bool $unreadOnly
   * @return LengthAwarePaginator
   */
  public function list(int $userId, int $perPage = 10, int $page = 1, bool $unreadOnly = false): LengthAwarePaginator
  {
    return $this->notificationRepository->getUserNotifications($userId, $perPage, $page, $unreadOnly);
  }

  /**
   * Get unread count notifications of user
   * @param int $userId
   * @return int
   */
  public function getUnreadCount($userId)
  {
    return $this->notificationRepository->getUnreadCount($userId);
  }

  /**
   * Mark notification as read
   * @param int $id
   * @return void
   */
  public function markAsRead($id)
  {
    $this->notificationRepository->markAsRead($id);
  }

  /**
   * Mark notification as unread
   * @param int $id
   * @return void
   */
  public function markAsUnread($id)
  {
    $this->notificationRepository->markAsUnread($id);
  }

  /**
   * Mark all notifications as read
   * @param int $userId
   * @return void
   */
  public function markAllAsRead($userId)
  {
    $this->notificationRepository->markAllAsRead($userId);
  }
}
