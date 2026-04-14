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