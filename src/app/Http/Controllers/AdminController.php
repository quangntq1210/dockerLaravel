<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Services\AdminService;

class AdminController extends Controller
{

    protected $adminService;

    /**
     * Constructor
     * @param AdminService $adminService
     * @return void
     */
    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    /**
     * Display a dashboard.
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     */
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
