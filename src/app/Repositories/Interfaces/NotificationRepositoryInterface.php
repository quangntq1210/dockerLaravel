<?php

namespace App\Repositories\Interfaces;

interface NotificationRepositoryInterface
{
    /**
     * Lấy danh sách notifications của user với pagination
     */
    public function getUserNotifications($userId, $perPage = 20, $page = 1);

    /**
     * Lấy số lượng unread notifications của user
     */
    public function getUnreadCount($userId);

    /**
     * Lấy notification theo ID
     */
    public function getById($id);

    /**
     * Mark notification as read
     */
    public function markAsRead($id);

    /**
     * Mark all notifications as read (bulk)
     */
    public function markAllAsRead($userId);

    /**
     * Tạo notification mới
     */
    public function create($data);

    /**
     * Xóa notification
     */
    public function delete($id);

    /**
     * Lấy unread notifications
     */
    public function getUnread($userId);
}
