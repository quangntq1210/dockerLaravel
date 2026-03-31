<?php

namespace App\Repositories\Interfaces;

interface NotificationRepositoryInterface
{
    /**
     * Get user notifications with pagination
     */
    public function getUserNotifications($userId, $perPage = 20, $page = 1);

    /**
     * Get unread count of user
     */
    public function getUnreadCount($userId);

    /**
     * Get notification by ID
     */
    public function getById($id);

    /**
     * Mark notification as read
     */
    public function markAsRead($id);

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead($userId);

    /**
     * Mark notification as unread by ID
     */
    public function markAsUnread($id);

    /**
     * Create new notification
     */
    public function create($data);

    /**
     * Delete notification by ID
     */
    public function delete($id);

    /**
     * Get unread notifications
     */
    public function getUnread($userId);

    /**
     * Get total notifications
     * @return int
     */
    public function getTotal();
}
