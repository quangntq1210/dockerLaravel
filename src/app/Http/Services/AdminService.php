<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\App; 
use App\Repositories\Interfaces\DashboardRepositoryInterface;
use App\Repositories\Interfaces\AdminServiceInterface;

// class AdminService implements AdminServiceInterface
// {
//     protected $dashboardRepo;

//     public function __construct(DashboardRepositoryInterface $dashboardRepo)
//     {
//         $this->dashboardRepo = $dashboardRepo;
//     }

//     public function getDashboardData($request): array
//     {
//         $filters = $this->parseFilters($request);
//         $page = $request->query('page', 1);
        
      
//         $locale = App::getLocale();
//         $cacheKey = 'dashboard_' . $locale . '_' . md5(json_encode($filters) . '_page_' . $page);

       
//         return Cache::remember($cacheKey, 60, function () use ($filters) {
//             $reportData = $this->dashboardRepo->getCampaignReport($filters);
//             $stats = $this->dashboardRepo->getStats();

           
//             return [
//                 'stats' => $stats,
//                 'table' => [
//                     'items' => $reportData, 
//                     'pagination' => [
//                         'current_page' => $reportData->currentPage(),
//                         'last_page'    => $reportData->lastPage(),
//                         'per_page'     => $reportData->perPage(),
//                         'total'        => $reportData->total(),
//                     ]
//                 ]
//             ];
//         });
//     }

//     private function parseFilters($request): array
//     {
//         return [
//             'q'           => $request->query('q'),
//             'campaign_id' => $request->query('campaign_id'),
//             'from'        => $request->query('from'),
//             'to'          => $request->query('to'),
//         ];
//     }
// }

class AdminService implements AdminServiceInterface
{
    protected $dashboardRepo;

    public function __construct(DashboardRepositoryInterface $dashboardRepo)
    {
        $this->dashboardRepo = $dashboardRepo;
    }

    public function getDashboardData(array $params): array
    {
        $page = $params['page'] ?? 1;
        $locale = App::getLocale();
        $cacheKey = "dashboard_{$locale}_p{$page}_" . md5(json_encode($params));

        return Cache::remember($cacheKey, 60, function () use ($params) {
            return [
                'stats' => $this->dashboardRepo->getStats(),
                'table' => [
                    'items' => $this->dashboardRepo->getCampaignReport($params)
                ]
            ];
        });
    }
}