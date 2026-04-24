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
            ->with(['campaign:id,title,body'])
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
     * @return void
     */
    public function markAsRead($id)
    {
        Notification::findOrFail($id)->update(['read_at' => now()]);
    }

    /**
     * Mark notification as unread
     * @param mixed $id
     * @return void
     */
    public function markAsUnread($id)
    {
        Notification::findOrFail($id)->update(['read_at' => null]);
    }

    /**
     * @param int $userId
     * @return void
     */
    public function markAllAsRead($userId)
    {
        Notification::where('user_id', $userId)
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
     * Delete notification by ID
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        $notification = Notification::findOrFail($id);
        return $notification->delete();
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

    /**
     * Create bulk notifications
     * @param array $data
     * @return bool
     */
    public function createBulk($data)
    {
        return Notification::insert($data);
    }
}
