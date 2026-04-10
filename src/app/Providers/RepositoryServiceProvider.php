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
use Illuminate\Support\ServiceProvider;

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
        \App\Repositories\Interfaces\DashboardRepositoryInterface::class,
        \App\Repositories\Eloquent\DashboardRepository::class
    );
    $this->app->bind(
        \App\Repositories\Interfaces\AdminServiceInterface::class,
        \App\Http\Services\AdminService::class
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
