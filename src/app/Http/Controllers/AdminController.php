<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AdminService;

class AdminController extends Controller
{
    protected $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    public function dashboard(Request $request)
    {
        $result = $this->adminService->getDashboardData($request);

        if ($request->ajax()) {
            return response()->json([
               
                'table' => view('admin.partials.dashboard_table', [
                    'data' => $result['data']
                ])->render(),
                'stats' => $result['stats'] ?? [
                    'total_campaigns' => $result['data']->total(),
                    'total_subscribers' => $result['data']->sum('subscribers_count'), 
                ],
                'lang' => trans('messages'), 
            ]);
        }

        return view('admin.dashboard', $result);
    }
}