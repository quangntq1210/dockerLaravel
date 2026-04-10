<?php

namespace App\Repositories\Interfaces;

use Illuminate\Http\Request;

interface AdminServiceInterface
{
    public function getDashboardData(array $params): array;
}