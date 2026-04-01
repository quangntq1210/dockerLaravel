<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Campaign;
use App\Models\Subscriber;
use App\Models\Notification;
use Illuminate\Support\Facades\Cache;
use App\Repositories\Interfaces\CampaignRepositoryInterface;
use App\Repositories\Interfaces\SubscriberRepositoryInterface;
use App\Repositories\Interfaces\NotificationRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
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



