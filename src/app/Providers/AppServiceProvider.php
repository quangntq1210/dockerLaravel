<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use App\Repositories\Interfaces\NotificationRepositoryInterface;
use App\Repositories\Eloquent\NotificationRepository;
use Illuminate\Pagination\Paginator;
use App\Repositories\Interfaces\DashboardRepositoryInterface;
use App\Repositories\DashboardRepository;
use Illuminate\Support\Facades\App;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
         App::setLocale(session('locale') ?? config('app.locale'));
         Paginator::useBootstrap();
    }
}
