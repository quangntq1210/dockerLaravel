<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Services\ManagerUserService;
use Illuminate\Http\JsonResponse;

class ManagerUserController extends Controller
{
    protected $userService;

   
    public function __construct(ManagerUserService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
      return view('admin.user-manager');
    }

    public function getData(): JsonResponse
    {
        try {
            $users = $this->userService->getUserListData();
            $users->getCollection();
            return response()->json([
                'status' => 'success',
                'data' => $users
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi lấy dữ liệu.'
            ], 500);
        }
    }
}