<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    public function store(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('dashboard.redirect');
        }

        $request->user()->sendEmailVerificationNotification();

        toast('info','Link verifikasi telah dikirim ulang.');
        return back();
    }
}
