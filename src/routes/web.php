<?php

use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', 'App\Http\Controllers\Auth\LoginController@showLoginForm')->name('login');
Route::post('/login', 'App\Http\Controllers\Auth\LoginController@login')->name('login.post');
Route::post('/logout', 'App\Http\Controllers\Auth\LoginController@logout')->name('logout');

Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', 'App\Http\Controllers\AdminController@index')->name('admin.dashboard');
    Route::resource('campaigns', 'App\Http\Controllers\CampaignController');
    Route::get('/subscribers/search', 'App\Http\Controllers\SubscriberController@search');
});

Route::middleware(['auth', 'role:user'])->group(function () {

    Route::get('/notifications', [NotificationController::class, 'index'])
        ->name('notifications.index');

    Route::prefix('api/user')->group(function () {
        Route::get('/notifications', [NotificationController::class, 'list'])->name('api.user.notifications.list');
        Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('api.user.notifications.unread-count');
        Route::put('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('api.user.notifications.read-all');
        Route::put('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('api.user.notifications.read');
        Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('api.user.notifications.destroy');
    });
});
