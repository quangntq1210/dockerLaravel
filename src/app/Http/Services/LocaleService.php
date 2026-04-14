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

    //   return [
    //     'sidebar' => [
    //         'title'     => __('sidebar.title'),
    //         'dashboard' => __('sidebar.dashboard'),
    //         'schedule'  => __('sidebar.schedule'),
    //         'logout'    => __('sidebar.logout'),
    //         'user_manager' => __('sidebar.user_manager'),
    //     ],
    //     'dashboard' => [
    //         'title'            => __('dashboard.title'),
    //         'total_campaign'   => __('dashboard.total_campaign'), 
    //         'subscriber'       => __('dashboard.subscriber'),
    //         'report'           => __('dashboard.report'),
    //         'filter'           => __('dashboard.filter'),
    //         'search_placeholder' => __('dashboard.search_placeholder'),
    //     ],
 
    //     'message' => [
    //         'loading'       => __('message.loading'),
    //         'no_data'       => __('message.no_data'),
    //         'server_error'  => __('message.server_error'),
    //         'verified'      => __('message.verified'),
    //         'not_verified'  => __('message.not_verified'),
    //         'view_hash'     => __('message.view_hash'),
    //         'id'            => __('message.id'),
    //         'username'      => __('message.username'),
    //         'email'         => __('message.email'),
    //         'password_hash' => __('message.password_hash'),
    //         'role'          => __('message.role'),
    //         'status'        => __('message.status'),
    //     ]
    // ];
    return [
            'sidebar'   => __ ('sidebar'),   
            'dashboard' => __ ('dashboard'), 
            'message'   => __ ('message'),  
        ];
    }

    public function getLocale(): string
    {
        return Session::get('locale', config('app.locale'));
    }
}