<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\AccountDeletionLog;
use App\Models\Province;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function show()
    {
        return view('profile.show')->with('title','Profil Saya');
    }

    public function edit()
    {
        $user = auth()->user();
        $provinces = Province::orderBy('id')->get();

        return view('profile.edit', compact('user', 'provinces'))
            ->with('title', 'Edit Profil');
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'phone'       => 'nullable|string',
            'province_id' => 'nullable|exists:provinces,id',
            'regency_id'  => 'nullable|exists:regencies,id',
            'avatar'      => 'nullable|image|max:2048',
        ]);

        if (!$request->filled('province_id')) {
            unset($data['province_id'], $data['regency_id']);
        }

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->avatar->store('avatars', 'public');
        }

        $user->update($data);

        toast('success','Profil berhasil diperbarui.');

        return redirect()->route('profile.show');
    }

    public function password()
    {
        return view('profile.password')->with('title','Ganti Password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:6|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            toast('error','Password saat ini salah.');

            return back();
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        toast('success','Password berhasil diubah.');

        return redirect()->route('profile.show');
    }

    public function delete()
    {
        return view('profile.delete')->with('title','Hapus Akun');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'reason_option' => 'required|string',
            'reason_custom' => 'nullable|string',
        ]);

        $user = auth()->user();

        AccountDeletionLog::create([
            'user_id'       => $user->id,
            'reason_option' => $request->reason_option,
            'reason_custom' => $request->reason_option === 'Lainnya'
                                ? $request->reason_custom
                                : null,
            'deactivated_at' => now(),
            'deleted_at'     => null,
        ]);

        $user->is_active = 0;
        $user->save();

        auth()->logout();

        toast('info','Akun dinonaktifkan. Anda bisa mengaktifkan kembali dengan login dalam 10 hari.');

        return redirect('/');
    }
}
