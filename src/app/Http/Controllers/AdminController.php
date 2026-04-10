<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Interfaces\AdminServiceInterface;

class AdminController extends Controller
{
    protected $adminService;

    public function __construct(AdminServiceInterface $adminService)
    {
        $this->adminService = $adminService;
    }

    public function dashboard(Request $request)
    {
        $result = $this->adminService->getDashboardData($request->all());

        if ($request->ajax()) {
            return response()->json([
                'table' => view('admin.partials.dashboard_table', [
                    'data' => $result['table']['items']
                ])->render(),
                'stats' => $result['stats']
            ]);
        }

        return view('admin.dashboard', $result);
    }
}