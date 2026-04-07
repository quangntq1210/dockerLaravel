<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CampaignSchedulingController;
use App\Http\Controllers\SubscriberController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\AddNewCampaignController;

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

// Cập nhật ngôn ngữ
Route::put('/locale', [LocaleController::class, 'update'])->name('locale.update');

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
| Admin Routes (Prefix 'admin' đã bao gồm cho tất cả route bên trong)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    
    // Dashboard chính
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    // Quản lý lịch trình chiến dịch (Campaign Scheduling)
    Route::get('/campaign-scheduling', [CampaignSchedulingController::class, 'index'])->name('admin.campaign-scheduling');
    Route::post('/campaign-scheduling', [CampaignSchedulingController::class, 'store'])->name('admin.campaign-scheduling.store');
    
    // Tìm kiếm Subscriber (AJAX)
    Route::get('/subscribers/search', [SubscriberController::class, 'search'])->name('admin.subscribers.search');
    
    // Tạo chiến dịch mới từ Modal (AJAX)
    // URL thực tế: http://localhost:8088/admin/campaigns/store
    Route::post('/campaigns/store', [AddNewCampaignController::class, 'store'])->name('admin.campaigns.store');
});

/*
|--------------------------------------------------------------------------
| User Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:user'])->group(function () {
    
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');

    Route::prefix('api/user')->group(function () {
        Route::get('/notifications', [NotificationController::class, 'list'])->name('api.user.notifications.list');
        Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('api.user.notifications.unread-count');
        Route::put('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('api.user.notifications.read-all');
        Route::put('/notifications/read/{id?}', [NotificationController::class, 'markAsRead'])->name('api.user.notifications.read');
        Route::put('/notifications/unread/{id?}', [NotificationController::class, 'markAsUnread'])->name('api.user.notifications.unread');
        Route::delete('/notifications/{id?}', [NotificationController::class, 'destroy'])->name('api.user.notifications.destroy');
    });
});