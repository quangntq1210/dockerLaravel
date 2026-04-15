<?php

namespace App\Repositories\Eloquent;

use App\Models\Campaign;
use App\Models\Subscriber;
use App\Repositories\Interfaces\DashboardRepositoryInterface;

class DashboardRepository implements DashboardRepositoryInterface
{
  public function getCampaignReport(array $filters)
{
    return Campaign::query()
        
        ->when($filters['q'] ?? null, function ($query, $search) {
            $query->where('title', 'like', "%{$search}%");
        })
     
        ->when($filters['campaign_id'] ?? null, function ($query, $id) {
            $query->where('id', $id);
        })
        ->paginate(10);
}

    public function getStats(): array
    {
        return [
            'total_campaigns' => Campaign::count(),
            'total_subscribers' => Subscriber::count(),
        ];
    }
}