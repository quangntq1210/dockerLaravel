<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Services\ManagerUserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ManagerUserController extends Controller
{
    protected $userService;

    public function __construct(ManagerUserService $userService)
    {
        $this->userService = $userService;
    }

  
    // VIEW PAGE
   
    public function index()
    {
        return view('admin.user-manager');
    }

  
    // GET USER DATA (AJAX)
  
    public function getData(): JsonResponse
    {
        try {
            $users = $this->userService->getUserListData();

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

  
    // DELETE USER (SOFT DELETE)
    
    public function delete(Request $request): JsonResponse
    {
        try {
            $this->userService->deleteUser($request->id);

            return response()->json([
                'status' => 'success',
                'message' => 'Xoá user thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }


    // RESTORE USER
    
    public function restore(Request $request): JsonResponse
    {
        try {
            $this->userService->restoreUser($request->id);

            return response()->json([
                'status' => 'success',
                'message' => 'Khôi phục user thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }

  
    // FORCE DELETE (optional)
    
    public function forceDelete(Request $request): JsonResponse
    {
        try {
            $this->userService->forceDeleteUser($request->id);

            return response()->json([
                'status' => 'success',
                'message' => 'Xoá vĩnh viễn user thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }
}