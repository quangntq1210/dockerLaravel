<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CampaignSchedulingController;
use App\Http\Controllers\SubscriberController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\CampaignRecipientController;
use App\Http\Controllers\HomeController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::post('/subscribe', [HomeController::class, 'store'])->name('home.store');

Route::put('/locale', [LocaleController::class, 'update'])
    ->name('locale.update');

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

Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/campaign-scheduling', [CampaignSchedulingController::class, 'index'])->name('admin.campaign-scheduling');
    Route::post('/campaign-scheduling', [CampaignSchedulingController::class, 'store'])->name('admin.campaign-scheduling.store');
    Route::get('/subscribers/search', [SubscriberController::class, 'search'])->name('admin.subscribers.search');
});

/*
|--------------------------------------------------------------------------
| User Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/campaigns-draft', [CampaignController::class, 'getCampaignsDraft'])
        ->name('campaigns.draft');
    Route::post('/campaigns-recipients', [CampaignRecipientController::class, 'storeBulk'])
        ->name('campaigns.recipients.store.bulk');
    Route::get('/notifications', [NotificationController::class, 'index'])
        ->name('notifications.index');

    Route::prefix('api/user')->group(function () {
        Route::get('/notifications', [NotificationController::class, 'list'])->name('api.user.notifications.list');
        Route::put('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('api.user.notifications.read-all');
        Route::put('/notifications/read/{id?}', [NotificationController::class, 'markAsRead'])->name('api.user.notifications.read');
        Route::put('/notifications/unread/{id?}', [NotificationController::class, 'markAsUnread'])->name('api.user.notifications.unread');
        Route::delete('/notifications/{id?}', [NotificationController::class, 'destroy'])->name('api.user.notifications.destroy');
    });
});

require __DIR__.'/auth.php';
