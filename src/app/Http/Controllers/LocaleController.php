<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Services\LocaleService;
use App\Http\Services\AdminService;

// class LocaleController extends Controller
// {
//     protected $localeService;

//     public function __construct(LocaleService $localeService)
//     {
//         $this->localeService = $localeService;
//     }

//     public function update(Request $request)
//     {
//         $request->validate([
//             'locale' => 'required|in:vi,en'
//         ]);

//         $payload = $this->localeService->changeLocale(
//             $request->input('locale'),
//             $request->boolean('withAdminPayload')
//         );
        
//         return response()->json($payload);
//     }
// }

class LocaleController extends Controller
{
    protected $localeService;
    protected $adminService; 

    public function __construct(LocaleService $localeService, AdminService $adminService)
    {
        $this->localeService = $localeService;
        $this->adminService = $adminService; 
    }
    public function update(Request $request)
    {
        $request->validate(['locale' => 'required|in:vi,en']);
        
        session(['locale' => $request->locale]);
        app()->setLocale($request->locale);

        
        $dashboardData = $this->adminService->getDashboardData($request);

        return response()->json([
            'status' => 'success',
            'table'  => view('admin.partials.dashboard_table', ['data' => $dashboardData['data']])->render(),
            'stats'  => $dashboardData['stats'],
            'lang'   => [
                'dashboard' => __('dashboard', [], $request->locale),
                'sidebar'   => __('sidebar', [], $request->locale), 
            ]
        ]);
    }
}