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
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/campaign-scheduling', [CampaignSchedulingController::class, 'index'])->name('admin.campaign-scheduling');
    Route::post('/campaign-scheduling', [CampaignSchedulingController::class, 'store'])->name('admin.campaign-scheduling.store');
    Route::get('/subscribers/search', [SubscriberController::class, 'search'])->name('admin.subscribers.search');
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
    Route::get('/notifications', [NotificationController::class, 'index'])
        ->name('notifications.index');

    Route::prefix('api/user')->group(function () {
        Route::get('/notifications', [NotificationController::class, 'list'])->name('api.user.notifications.list');
        Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('api.user.notifications.unread-count');
        Route::put('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('api.user.notifications.read-all');
        Route::put('/notifications/read/{id?}', [NotificationController::class, 'markAsRead'])->name('api.user.notifications.read');
        Route::put('/notifications/unread/{id?}', [NotificationController::class, 'markAsUnread'])->name('api.user.notifications.unread');
        Route::delete('/notifications/{id?}', [NotificationController::class, 'destroy'])->name('api.user.notifications.destroy');
    });
});

// locale change
Route::post('/locale/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'vi'])) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('locale.change');
