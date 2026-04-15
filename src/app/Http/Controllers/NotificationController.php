<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Http\Services\NotificationService;

class NotificationController extends ApiController
{
    protected $notificationService;

    /**
     * Constructor
     * @param NotificationService $notificationService
     * @return void
     */
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * View notifications
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('user.notifications');
    }

    /**
     * Remove notification by ID
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function destroy($id)
    {
        $this->notificationService->delete($id);
        return $this->success(__('message.notification_deleted'));
    }

    /**
     * List notifications
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function list(Request $request): JsonResponse
    {
        $userId = auth()->id();
        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);
        $unreadOnly = $request->boolean('unread');

        $notifications = $this->notificationService->list($userId, $perPage, $page, $unreadOnly);

        return $this->success(
            __('message.notifications_fetched'),
            $notifications->items(),
            [
                'current_page' => $notifications->currentPage(),
                'per_page'     => $notifications->perPage(),
                'last_page'    => $notifications->lastPage(),
                'total'        => $notifications->total(),
                'unread_count' => $this->notificationService->getUnreadCount($userId),
            ],
            200
        );
    }

    /**
     * Mark notification as read
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsRead($id)
    {
        $this->notificationService->markAsRead($id);
        return $this->success(__('message.notification_marked_as_read'), null, null, 200);
    }

    /**
     * Mark notification as unread
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsUnread($id)
    {
        $this->notificationService->markAsUnread($id);
        return $this->success(__('message.notification_marked_as_unread'), null, null, 200);
    }

    /**
     * Mark all notifications as read
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAllAsRead()
    {
        $userId = auth()->id();
        $this->notificationService->markAllAsRead($userId);
        return $this->success(__('message.notifications_marked_as_read'), null, null, 200);
    }
}
