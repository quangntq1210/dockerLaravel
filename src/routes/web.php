<?php

use App\Http\Controllers\NotificationController;
use App\Http\Controllers\CampaignSchedulingController;
use App\Http\Controllers\SubscriberController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/notifications');
Route::get('/login', 'App\Http\Controllers\Auth\LoginController@showLoginForm')->name('login');
Route::post('/login', 'App\Http\Controllers\Auth\LoginController@login')->name('login.post');
Route::post('/logout', 'App\Http\Controllers\Auth\LoginController@logout')->name('logout');

Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', 'App\Http\Controllers\AdminController@index')->name('admin.dashboard');
    Route::get('/campaign-scheduling', [CampaignSchedulingController::class, 'index'])->name('admin.campaign-scheduling');
    Route::post('/campaign-scheduling', [CampaignSchedulingController::class, 'store'])->name('admin.campaign-scheduling.store');
    Route::get('/subscribers/search', [SubscriberController::class, 'search'])->name('admin.subscribers.search');
});

Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])
        ->name('notifications.index');

    Route::prefix('api/user')->group(function () {
        Route::get('/notifications', [NotificationController::class, 'list'])->name('api.user.notifications.list');
        Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('api.user.notifications.unread-count');
        Route::put('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('api.user.notifications.read-all');
        Route::put('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('api.user.notifications.read');
        Route::put('/notifications/{id}/unread', [NotificationController::class, 'markAsUnread'])->name('api.user.notifications.unread');
        Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('api.user.notifications.destroy');
    });
});
