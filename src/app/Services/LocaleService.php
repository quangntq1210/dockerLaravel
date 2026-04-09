<?php
namespace App\Services;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use App\Models\Campaign;
class LocaleService
{
 
    public function updateLocale($locale)
    {
        session(['locale' => $locale]);
        App::setLocale($locale);
        $data = $this->getCampaignData();
        $table = view('admin.partials.dashboard_table', [
            'data' => $data
        ])->render();

        return [
            'table' => $table,
            'lang'  => [
                'dashboard' => trans('dashboard'),
                'sidebar'   => trans('sidebar'),
            ]
        ];
    }

    private function getCampaignData()
    {
        return Campaign::paginate(10);
    }
     public function getLocale()
    {
        return Session::get('locale', config('app.locale'));
    }

}

   
