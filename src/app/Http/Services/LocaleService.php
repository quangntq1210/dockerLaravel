<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Config;

class LocaleService
{
    public function persistLocale(string $locale): void
    {
       
        Session::put('locale', $locale);
     
        App::setLocale($locale);
        Config::set('app.locale', $locale); 
    }

    public function getTranslations(string $locale): array
    {
      
        App::setLocale($locale);

        return [
            'sidebar' => [
                'title'     => __('sidebar.title'),
                'dashboard' => __('sidebar.dashboard'),
                'schedule'  => __('sidebar.schedule'),
                'logout'    => __('sidebar.logout'),
            ],
            'dashboard' => [
                'title'           => __('dashboard.title'),
                'total_campaign' => __('dashboard.total_campaign'), 
                'subscriber'      => __('dashboard.subscriber'),
                'report'          => __('dashboard.report'),
                'filter'          => __('dashboard.filter'),
                'search_placeholder' => __('dashboard.search_placeholder'),
            ]
        ];
    }

    public function getLocale(): string
    {
        return Session::get('locale', config('app.locale'));
    }
}