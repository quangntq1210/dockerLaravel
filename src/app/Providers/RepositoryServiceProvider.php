<?php

namespace App\Providers;

use App\Repositories\Eloquent\CampaignRecipientsRepository;
use App\Repositories\Eloquent\CampaignRepository;
use App\Repositories\Eloquent\NotificationRepository;
use App\Repositories\Interfaces\CampaignRecipientsRepositoryInterface;
use App\Repositories\Interfaces\CampaignRepositoryInterface;
use App\Repositories\Interfaces\NotificationRepositoryInterface;
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
