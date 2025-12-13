<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Teacher;
use App\Models\Course;
use Illuminate\Http\Request;

class TentorController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->query('q');

        $query = Teacher::with('user', 'courses')
            ->when($q, function ($qr) use ($q) {

                // Filter berdasarkan nama tentor
                $qr->whereHas('user', function ($u) use ($q) {
                    $u->where('name', 'like', "%{$q}%");
                });

                // Atau berdasarkan nama course
                $qr->orWhereHas('courses', function ($c) use ($q) {
                    $c->where('name', 'like', "%{$q}%");
                });
            })
            ->orderBy('id', 'desc');

        $tentor = $query->paginate(12)->withQueryString();

        return view('tentor.index', compact('tentor', 'q'));
    }

    public function show($id)
    {
        $teacher = Teacher::with(['user', 'courses'])->findOrFail($id);
        return view('tentor.show', compact('teacher'));
    }

    public function create()
    {
        $users = User::role('siswa')->where('is_active', true)->get();
        $courses = Course::all();

        return view('tentor.create', compact('users', 'courses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id'   => 'required|exists:users,id',
            'course_id' => 'nullable|array',
            'bio'       => 'nullable|string',
        ]);

        $user = User::findOrFail($request->user_id);

        // Ubah role menjadi tentor
        $user->syncRoles('tentor');

        // Buat profil tentor
        $teacher = Teacher::create([
            'user_id' => $user->id,
            'bio'     => $request->bio,
        ]);

        // Assign courses ke pivot
        if ($request->course_id) {
            $teacher->courses()->sync($request->course_id);
        }

        toast('success','Tentor berhasil ditambahkan.');

        return redirect()->route('tentor.index');
    }

    public function edit($id)
    {
        $teacher = Teacher::with(['user', 'courses'])->findOrFail($id);
        $courses = Course::all();

        return view('tentor.edit', compact('teacher', 'courses'));
    }

    public function update(Request $request, $id)
    {
        $teacher = Teacher::findOrFail($id);

        $request->validate([
            'bio'       => 'nullable|string',
            'course_id' => 'nullable|array',
        ]);

        // Update Bio
        $teacher->update([
            'bio' => $request->bio,
        ]);

        // Sync pivot
        $teacher->courses()->sync($request->course_id ?? []);

        toast('success','Tentor berhasil diperbarui.');

        return redirect()->route('tentor.show', $teacher->id);
    }

    public function remove($id)
    {
        $teacher = Teacher::findOrFail($id);
        $user = $teacher->user;

        // Hapus relasi pivot
        $teacher->courses()->detach();

        // Kembalikan role menjadi siswa
        $user->syncRoles('siswa');

        // Hapus teacher profile
        $teacher->delete();

        toast('success','Tentor berhasil dihapus dan dikembalikan menjadi siswa.');

        return redirect()->route('tentor.index');
    }

    public function toggleActive($id)
    {
        $teacher = Teacher::findOrFail($id);
        $user = $teacher->user;

        $user->is_active = !$user->is_active;
        $user->save();

        toast('success','Status tentor berhasil diubah.');

        return back();
    }
}
