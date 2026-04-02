<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use App\Repositories\Interfaces\DashboardRepositoryInterface;
class AdminService
{
    protected $dashboardRepo;

    public function __construct(DashboardRepositoryInterface $dashboardRepo)
    {
        $this->dashboardRepo = $dashboardRepo;
    }

    public function getDashboardData($request)
    {
        $filters = [
            'q' => $request->q,
            'campaign_id' => $request->campaign_id,
            'from' => $request->from,
            'to' => $request->to
        ];

        $cacheKey = 'dashboard_' . md5(json_encode($filters) . request()->page);

        return Cache::remember($cacheKey, 60, function () use ($filters) {
            return [
                'stats' => $this->dashboardRepo->getStats(),
                'data' => $this->dashboardRepo->getCampaignReport($filters)
            ];
        });
    }
}