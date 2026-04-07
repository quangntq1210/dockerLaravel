<?php
namespace App\Http\Services;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use App\Models\Campaign;
class LocaleService
{
    /**
     * Persist the locale in the session and set the locale in the application
     * @param string $locale
     * @return void
     */
    public function persistLocale(string $locale): void
    {
        session(['locale' => $locale]);
        App::setLocale($locale);
    }
 
    /**
     * Build the locale payload for the admin dashboard
     * @return array
     */
    public function buildAdminLocalePayload()
    {
        $data = $this->getCampaignData();
        $table = view('admin.partials.dashboard_table', [
            'data' => $data,
        ])->render();
        return [
            'table' => $table,
            'lang' => [
                'dashboard' => trans('dashboard'),
                'sidebar' => trans('sidebar'),
            ],
        ];
    }

    /**
     * Change the locale and return the payload
     * @param string $locale
     * @param bool $withAdminPayload
     * @return array
     */
    public function changeLocale(string $locale, bool $withAdminPayload): array
    {
        $this->persistLocale($locale);
        if (!$withAdminPayload) {
            return ['locale' => $locale];
        }
        return $this->buildAdminLocalePayload();
    }


    /**
     * Get the campaign data
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    private function getCampaignData()
    {
        return Campaign::paginate(10);
    }
    
    /**
     * Get the locale from the session
     * @return string
     */
     public function getLocale()
    {
        return Session::get('locale', config('app.locale'));
    }

}

   
