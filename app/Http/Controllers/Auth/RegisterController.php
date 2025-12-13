<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Province;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function show()
    {
        return view('auth.register', [
            'provinces' => Province::orderBy('id')->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'               => 'required|string|max:255',
            'email'              => 'required|email|unique:users,email',
            'phone'              => 'nullable|string',
            'password'           => 'required|min:6|confirmed',
            'province_id'        => 'required|exists:provinces,id',
            'regency_id'         => 'required|exists:regencies,id',
            'avatar'             => 'nullable|image|max:2048',
        ]);

        // Upload avatar
        if ($request->hasFile('avatar')) {
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        } else {
            $validated['avatar'] = 'img/user.png';
        }

        // Create user
        $user = User::create([
            'name'        => $validated['name'],
            'email'       => $validated['email'],
            'password'    => Hash::make($validated['password']),
            'phone'       => $validated['phone'],
            'province_id' => $validated['province_id'],
            'regency_id'  => $validated['regency_id'],
            'avatar'      => $validated['avatar'],
        ]);

        $user->assignRole('siswa');
        $user->sendEmailVerificationNotification();
        // auto login user
        auth()->login($user);

        toast('success','Akun berhasil dibuat. Silakan verifikasi email Anda.');
        return redirect()->route('verification.notice');
    }
}
