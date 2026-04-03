<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CampaignSchedulingController;
use App\Http\Controllers\SubscriberController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\LocaleController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->role === 'admin'
            ? redirect()->route('admin.dashboard')
            : redirect()->route('notifications.index');
    }
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/

Route::controller(LoginController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'login')->name('login.post');
    Route::post('/logout', 'logout')->name('logout');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->middleware(['auth', 'role:admin'])
    ->as('admin.')
    ->group(function () {

        Route::get('/dashboard', [AdminController::class, 'dashboard'])
            ->name('dashboard');

        Route::get('/campaign-scheduling', [CampaignSchedulingController::class, 'index'])
            ->name('campaigns.index');

        Route::post('/campaign-scheduling', [CampaignSchedulingController::class, 'store'])
            ->name('campaigns.store');

        Route::get('/subscribers/search', [SubscriberController::class, 'search'])
            ->name('subscribers.search');
    });

/*
|--------------------------------------------------------------------------
| User Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:user'])->group(function () {

    Route::get('/notifications', [NotificationController::class, 'index'])
        ->name('notifications.index');

});

/*
|--------------------------------------------------------------------------
| User API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('api/user')
    ->middleware(['auth', 'role:user'])
    ->name('api.user.')
    ->group(function () {

        Route::prefix('notifications')->name('notifications.')->group(function () {

            Route::get('/', [NotificationController::class, 'list'])->name('list');
            Route::get('/unread-count', [NotificationController::class, 'unreadCount'])->name('unread-count');

            Route::put('/read-all', [NotificationController::class, 'markAllAsRead'])->name('read-all');
            Route::put('/{id}/read', [NotificationController::class, 'markAsRead'])->name('read');
            Route::put('/{id}/unread', [NotificationController::class, 'markAsUnread'])->name('unread');

            Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('destroy');
        });

    });

/*
|--------------------------------------------------------------------------
| Locale Switch
|--------------------------------------------------------------------------
*/

Route::put('/locale', [LocaleController::class, 'update'])
    ->name('locale.update');