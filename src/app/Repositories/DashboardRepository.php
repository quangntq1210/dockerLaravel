<?php

namespace App\Repositories;

use App\Repositories\Interfaces\DashboardRepositoryInterface;
use Illuminate\Support\Facades\DB;
use App\Models\Campaign;
use App\Models\Subscriber;
use Carbon\Carbon;

class DashboardRepository implements DashboardRepositoryInterface
{
    public function getStats()
    {
        return [
            'total_campaigns' => Campaign::count(),
            'total_subscribers' => Subscriber::count(),
        ];
    }

    public function getCampaignReport($filters)
    {
        $query = DB::table('campaigns')
            ->join('campaign_recipients', 'campaigns.id', '=', 'campaign_recipients.campaign_id');

        if (!empty($filters['q'])) {
            $query->join('subscribers', 'campaign_recipients.subscriber_id', '=', 'subscribers.id');

            $query->where(function ($q) use ($filters) {
                $q->where('subscribers.email', 'like', "%{$filters['q']}%")
                  ->orWhere('subscribers.name', 'like', "%{$filters['q']}%");
            });
        }

        if (!empty($filters['campaign_id'])) {
            $query->where('campaigns.id', $filters['campaign_id']);
        }

        if (!empty($filters['from']) && !empty($filters['to'])) {
            $query->whereBetween('campaigns.send_at', [
                Carbon::parse($filters['from'])->startOfDay(),
                Carbon::parse($filters['to'])->endOfDay()
            ]);
        }

        $query->select(
            'campaigns.id',
            'campaigns.title',
            'campaigns.status',
            'campaigns.send_at',
            DB::raw('COUNT(*) as total'),
            DB::raw("SUM(campaign_recipients.status = 'sent') as sent"),
            DB::raw("SUM(campaign_recipients.status = 'failed') as failed")
        );

        $query->groupBy('campaigns.id');
        return $query->orderByDesc('campaigns.id')->paginate(10);
    }
}