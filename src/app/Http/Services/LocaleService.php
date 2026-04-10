<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LocaleService
{
  
    public function persistLocale(string $locale): void
    {
        Session::put('locale', $locale);
        App::setLocale($locale);
    }

   
    public function getTranslations(string $locale): array
    {
       
        return [
            'sidebar' => [
                'title'     => __('sidebar.title', [], $locale),
                'dashboard' => __('sidebar.dashboard', [], $locale),
                'schedule'  => __('sidebar.schedule', [], $locale),
                'logout'    => __('sidebar.logout', [], $locale),
            ],
            'dashboard' => [
                'title' => __('dashboard.title', [], $locale),
                'total_campaigns' => __('dashboard.total_campaigns', [], $locale),
            ]
        ];
    }

   
    public function getLocale(): string
    {
        return Session::get('locale', config('app.locale'));
    }
}