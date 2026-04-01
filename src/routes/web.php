<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CampaignSchedulingController;
use App\Http\Controllers\SubscriberController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\LocaleController;


Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->role === 'admin'
            ? redirect()->route('admin.dashboard')
            : redirect()->route('notifications.index');
    }
    return redirect()->route('login');
});


Route::controller(LoginController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'login')->name('login.post');
    Route::post('/logout', 'logout')->name('logout');
});

Route::prefix('admin')
    ->middleware(['auth', 'role:admin'])
    ->as('admin.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [AdminController::class, 'dashboard'])
            ->name('dashboard');

        // Campaign Scheduling (RESTful)
        Route::controller(CampaignSchedulingController::class)->group(function () {
            Route::get('/campaigns', 'index')->name('campaigns.index');
            Route::post('/campaigns', 'store')->name('campaigns.store');
        });

        // Subscribers
        Route::controller(SubscriberController::class)->group(function () {
            Route::get('/subscribers/search', 'search')->name('subscribers.search');
        });
});
// route language switch
Route::put('/locale', [LocaleController::class, 'update'])
    ->name('locale.update');

Route::middleware(['auth', 'role:user'])->group(function () {

    // Notification page
    Route::get('/notifications', [NotificationController::class, 'index'])
        ->name('notifications.index');

    Route::prefix('api/user/notifications')
        ->as('api.user.notifications.')
        ->group(function () {

            Route::get('/', [NotificationController::class, 'list'])->name('list');
            Route::get('/unread-count', [NotificationController::class, 'unreadCount'])->name('unread-count');

            Route::put('/read-all', [NotificationController::class, 'markAllAsRead'])->name('read-all');
            Route::put('/{id}/read', [NotificationController::class, 'markAsRead'])->name('read');
            Route::put('/{id}/unread', [NotificationController::class, 'markAsUnread'])->name('unread');

            Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('destroy');
        });
});