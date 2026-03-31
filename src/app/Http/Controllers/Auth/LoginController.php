<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // Hiển thị form
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Xử lý đăng nhập
    // Xử lý đăng nhập
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();

            // Lấy thông tin user vừa đăng nhập
            $user = Auth::user();

            // Kiểm tra Role: Nếu là 'admin' thì vào trang quản trị
            if ($user->role == 'admin') {
                return redirect()->intended('/admin/dashboard');
            }

            // Nếu là 'user' hoặc role khác thì về trang chủ hoặc trang thông báo
            return redirect()->intended('/notifications');
        }

        return back()->withErrors([
            'email' => 'Thông tin đăng nhập không chính xác.',
        ])->onlyInput('email');
    }

    // Đăng xuất
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
