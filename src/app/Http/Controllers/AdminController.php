<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Campaign;
use App\Models\Subscriber;
use App\Models\Notification;
use Illuminate\Support\Facades\Cache; // Để sau này làm Task 4.4 của Thắng

class AdminController extends Controller
{
    /**
     * Trang Dashboard chính của Admin
     */
    public function index()
    {
        // Tạm thời lấy dữ liệu trực tiếp, sau này Quang sẽ phối hợp với Thắng để dùng Cache
        $stats = [
            'total_campaigns' => Campaign::count(),
            'total_subscribers' => Subscriber::count(),
            'pending_jobs' => Campaign::where('status', 'scheduled')->count(),
            'sent_notifications' => Notification::count(),
        ];

        return view('layouts.dashboard', compact('stats')); 
    }
}