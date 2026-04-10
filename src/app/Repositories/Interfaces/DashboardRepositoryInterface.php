<?php

namespace App\Repositories\Interfaces;

interface DashboardRepositoryInterface
{
    public function getCampaignReport(array $filters);
    public function getStats(): array;
}