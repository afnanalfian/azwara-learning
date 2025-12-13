<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AccountDeletionLog;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Email atau password salah.']);
        }
        $request->session()->regenerate();
        // Ambil model user yang sedang login
        $user = Auth::user();

        // Kalau akun sebelumnya dinonaktifkan (is_active = 0),
        // kita re-activate dan hapus log penghapusan yang belum final
        if ($user && ! $user->is_active) {
            $user->is_active = 1;
            $user->save();

            // Hapus (atau update) AccountDeletionLog yang belum ada deleted_at
            AccountDeletionLog::where('user_id', $user->id)
                ->whereNull('deleted_at')
                ->update(['deleted_at' => now()]);
        }


        return redirect()->route('dashboard.redirect');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
    public function forgotPasswordForm()
    {
        return view('auth.forgot-password');
    }
    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
                    ? back()->with('status', __($status))
                    : back()->withErrors(['email' => __($status)]);
    }
    public function resetPasswordForm($token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }
    public function updatePassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password)
                ])->save();
            }
        );
        if ($status === Password::PASSWORD_RESET) {
            toast('success','Password berhasil direset, silakan login.');
            return redirect()->route('login');
        }

        session()->push('toasts', [
            'type' => 'error',
            'message' => __($status),
        ]);
        return back();
    }
}
