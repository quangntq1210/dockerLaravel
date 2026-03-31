<?php

namespace App\Repositories\Eloquent;

use App\Models\Notification;
use App\Repositories\Interfaces\NotificationRepositoryInterface;

class NotificationRepository implements NotificationRepositoryInterface
{
    /**
     * @param int $userId
     * @param int $perPage
     * @param int $page
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getUserNotifications($userId, $perPage = 20, $page = 1, bool $unreadOnly = false)
    {
        return Notification::where('user_id', $userId)
            ->when($unreadOnly, fn($q) => $q->whereNull('read_at'))
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * @param int $userId
     * @return int
     */
    public function getUnreadCount($userId)
    {
        return Notification::where('user_id', $userId)
            ->whereNull('read_at')
            ->count();
    }

    /**
     * @param int $id
     * @return Notification|null
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getById($id)
    {
        return Notification::find($id);
    }

    /**
     * @param int $id
     * @return Notification|null
     */
    public function markAsRead($id)
    {
        $notification = $this->getById($id);
        if ($notification) {
            $notification->update(['read_at' => now()]);
        }
        return $notification;
    }

    /**
     * Mark notification as unread
     * @param int $id
     * @return Notification|null
     */
    public function markAsUnread($id)
    {
        $notification = $this->getById($id);
        if ($notification) {
            $notification->update(['read_at' => null]);
        }
        return $notification;
    }

    /**
     * @param int $userId
     * @return int
     */
    public function markAllAsRead($userId)
    {
        return Notification::where('user_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    /**
     * @param array $data
     * @return Notification
     */
    public function create($data)
    {
        return Notification::create($data);
    }

    /**
     * @param int $id
     * @return Notification|null
     */
    public function delete($id)
    {
        $notification = $this->getById($id);
        if ($notification) {
            $notification->delete();
        }
        return $notification;
    }

    /**
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUnread($userId)
    {
        return Notification::where('user_id', $userId)
            ->whereNull('read_at')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get total notifications
     * @return int
     */
    public function getTotal()
    {
        return Notification::count();
    }
}
