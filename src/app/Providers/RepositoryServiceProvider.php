<?php

namespace App\Providers;

use App\Repositories\Eloquent\CampaignRecipientsRepository;
use App\Repositories\Eloquent\CampaignRepository;
use App\Repositories\Eloquent\NotificationRepository;
use App\Repositories\Eloquent\SubscriberRepository;
use App\Repositories\Eloquent\DashboardRepository;
use App\Repositories\Interfaces\DashboardRepositoryInterface;
use App\Repositories\Interfaces\CampaignRecipientsRepositoryInterface;
use App\Repositories\Interfaces\CampaignRepositoryInterface;
use App\Repositories\Interfaces\NotificationRepositoryInterface;
use App\Repositories\Interfaces\SubscriberRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Eloquent\UserRepository;
use Illuminate\Support\ServiceProvider;
use App\Repositories\Interfaces\AdminServiceInterface;
use App\Http\Services\AdminService;
use App\Services\AuthService;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            NotificationRepositoryInterface::class,
            NotificationRepository::class
        );

        $this->app->bind(
            CampaignRepositoryInterface::class,
            CampaignRepository::class
        );

        $this->app->bind(
            CampaignRecipientsRepositoryInterface::class,
            CampaignRecipientsRepository::class
        );

        $this->app->bind(
            SubscriberRepositoryInterface::class,
            SubscriberRepository::class
        );

        $this->app->bind(
            DashboardRepositoryInterface::class,
            DashboardRepository::class
        );

        $this->app->bind(
            UserRepositoryInterface::class,
            UserRepository::class
        );

        $this->app->bind(
            AdminServiceInterface::class,
            AdminService::class
        );

        $this->app->bind(
            AuthService::class
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
