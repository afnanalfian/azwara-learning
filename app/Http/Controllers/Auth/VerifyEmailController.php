<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class VerifyEmailController extends Controller
{
    public function __invoke(EmailVerificationRequest $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('dashboard.redirect');
        }

        $request->fulfill();

        toast('success','Akun Anda berhasil diverifikasi!');
        return redirect()->route('dashboard.redirect');
    }
}
