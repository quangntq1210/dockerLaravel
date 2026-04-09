<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\AccountMail;
use Illuminate\Auth\Events\Verified;
use Symfony\Component\HttpFoundation\Request;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     *
     * @param  \Symfony\Component\HttpFoundation\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function __invoke(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);
        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            abort(403);
        }
        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            event(new Verified($user));
        }

        try {
            Mail::to($user->email)->send(new AccountMail($user));
        } catch (\Throwable $th) {
            throw new \RuntimeException('SMTP reported failures: ' . implode(',', Mail::failures()));
        }

        return redirect()->route('login')->with('status', 'verified');
    }
}
