<?php
namespace App\Contracts\Services;

use Illuminate\Http\Request;

interface DashboardServiceInterface {
    public function getDashboardMetrics(Request $request): array;
}