<?php

namespace App\Repositories\Interfaces;

interface DashboardRepositoryInterface
{
    public function getStats();

    public function getCampaignReport($filters);
}
