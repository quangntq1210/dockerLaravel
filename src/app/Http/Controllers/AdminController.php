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




// class AdminController extends Controller
// {
//     protected $campaignRepo;
//     protected $subscriberRepo;
//     protected $notificationRepo;

//     /**
//      * Constructor
//      */
//     public function __construct(
//         CampaignRepositoryInterface $campaignRepo,
//         SubscriberRepositoryInterface $subscriberRepo,
//         NotificationRepositoryInterface $notificationRepo
//     ) {
//         $this->campaignRepo = $campaignRepo;
//         $this->subscriberRepo = $subscriberRepo;
//         $this->notificationRepo = $notificationRepo;
//     }

//     /**
//      * Display the dashboard
//      */
//     public function index()
//     {
//         $stats = Cache::remember('admin.dashboard.stats', 300, function () {
//             return [
//                 'total_campaigns' => $this->campaignRepo->getTotal(),
//                 'total_subscribers' => $this->subscriberRepo->getTotal(),
//                 'pending_jobs' => $this->campaignRepo->getTotalByStatus('scheduled'),
//                 'sent_notifications' => $this->notificationRepo->getTotal(),
//             ];
//         });

//         return view('admin.dashboard', compact('stats'));
//     }
//}
