<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class SiswaController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->query('q'));

        $query = User::role('siswa')
            ->with(['province', 'regency'])
            ->when($q, function ($qr) use ($q) {
                $qr->where(function ($s) use ($q) {

                    // nama & phone
                    $s->where('name', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%")

                    // provinsi
                    ->orWhereHas('province', function ($p) use ($q) {
                        $p->where('name', 'like', "%{$q}%");
                    })

                    // kab / kota
                    ->orWhereHas('regency', function ($r) use ($q) {
                        $r->where('name', 'like', "%{$q}%");
                    });
                });
            })
            ->orderBy('name', 'asc');

        $siswa = $query->paginate(12)->withQueryString();

        return view('siswa.index', compact('siswa', 'q'));
    }

    public function show($id)
    {
        $user = User::role('siswa')->with(['province', 'regency'])->findOrFail($id);
        return view('siswa.show', compact('user'));
    }

    public function toggleActive($id)
    {
        $user = User::findOrFail($id);
        $user->is_active = !$user->is_active;
        $user->save();

        toast('warning','Status akun telah diubah diubah.');

        return back();
    }
}
