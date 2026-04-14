<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Services\PasswordService;
use App\Http\Requests\UpdatePasswordRequest;
use Illuminate\Http\RedirectResponse;
use Exception;

class PasswordController extends Controller
{
    protected $passwordService;

    public function __construct(PasswordService $passwordService)
    {
        $this->passwordService = $passwordService;
    }

    public function showQuickChangeForm()
    {
        return view('auth.change-password');
    }

    public function update(UpdatePasswordRequest $request)
    {
        try {
            $this->passwordService->quickChangePassword($request->validated());

            return redirect()->route('login')->with('status', 'Đổi mật khẩu thành công! Hãy đăng nhập lại.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}