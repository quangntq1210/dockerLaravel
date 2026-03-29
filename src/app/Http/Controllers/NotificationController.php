<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Repositories\Eloquent\NotificationRepository;
use App\Http\Controllers\ApiController;

class NotificationController extends ApiController
{
    protected $notificationRepo;

    public function __construct(NotificationRepository $notificationRepo)
    {
        $this->notificationRepo = $notificationRepo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('user.notifications');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $notification = $this->notificationRepo->delete($id);
        if (!$notification) {
            return $this->error('Notification not found', 404);
        }
        return $this->success('Notification deleted successfully', null, null, 200);
    }

    /**
     * List notifications
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(Request $request): JsonResponse
    {
        $userId = auth()->id();
        $notifications = $this->notificationRepo->getUserNotifications(
            $userId,
            $request->get('per_page', 10),
            $request->get('page', 1),
            $request->boolean('unread')
        );
        return $this->success('Notifications fetched successfully', [
            'data' => $notifications->items(),
            'meta' => [
                'current_page' => $notifications->currentPage(),
                'per_page'     => $notifications->perPage(),
                'total'        => $notifications->total(),
                'unread_count' => $this->notificationRepo->getUnreadCount($userId),
            ],
        ]);
    }

    /**
     * Mark notification as read
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsRead($id)
    {
        $notification = $this->notificationRepo->markAsRead($id);
        if (!$notification) {
            return $this->error('Notification not found', 404);
        }
        return $this->success('Notification marked as read successfully', null, null, 200);
    }

    /**
     * Mark notification as unread
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsUnread($id)
    {
        $notification = $this->notificationRepo->markAsUnread($id);
        if (!$notification) {
            return $this->error('Notification not found', 404);
        }
        return $this->success('Notification marked as unread successfully', null, null, 200);
    }

    /**
     * Get unread count
     * @return \Illuminate\Http\JsonResponse
     */
    public function unreadCount()
    {
        $count = $this->notificationRepo->getUnreadCount(auth()->id());
        return $this->success('Unread count fetched successfully', $count, null, 200);
    }

    /**
     * Mark all notifications as read
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAllAsRead()
    {
        $count = $this->notificationRepo->markAllAsRead(auth()->id());
        return $this->success('Notifications marked as read successfully', null, null, 200);
    }
}
