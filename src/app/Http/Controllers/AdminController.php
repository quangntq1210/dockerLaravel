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
                ])->render()
            ]);
        }

        return view('admin.dashboard', $result);
    }
}
