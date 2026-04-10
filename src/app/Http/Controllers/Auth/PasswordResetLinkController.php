<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Notifications\SendNewPasswordNotification; 

class PasswordResetLinkController extends Controller
{
   
    public function create()
    {
        return view('auth.forgot-password');
    }
    
    public function store(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if ($user) {
            $newPassword = Str::random(8);
            $user->password = Hash::make($newPassword);
            $user->save();
  
            $user->notify(new SendNewPasswordNotification($newPassword));
        }

        return back()->with('status', __('auth.password_reset_sent'));
    }
}