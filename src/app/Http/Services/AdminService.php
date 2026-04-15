<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\App; 
use App\Repositories\Interfaces\DashboardRepositoryInterface;
use App\Repositories\Interfaces\AdminServiceInterface;

class AdminService implements AdminServiceInterface
{
    protected $dashboardRepo;

    /**
     * Constructor
     * @param \App\Repositories\Interfaces\DashboardRepositoryInterface $dashboardRepo
     * @return void
     */
    public function __construct(DashboardRepositoryInterface $dashboardRepo)
    {
        $this->dashboardRepo = $dashboardRepo;
    }

    /**
     * Get dashboard data
     * @param array $params
     * @return array
     */
    public function getDashboardData(array $params): array
    {
        $page = $params['page'] ?? 1;
        $locale = App::getLocale();
        $cacheKey = "dashboard_{$locale}_p{$page}_" . md5(json_encode($params));

        return Cache::tags(['dashboard'])->remember($cacheKey, 300, function () use ($params) {
            return [
                'stats' => $this->dashboardRepo->getStats(),
                'table' => [
                    'items' => $this->dashboardRepo->getCampaignReport($params)
                ]
            ];
        });
    }
}