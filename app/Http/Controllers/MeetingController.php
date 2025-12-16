<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Meeting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class MeetingController extends Controller
{
    /**
     * Show meeting detail
     * (materi, video, status, post test)
     */
    public function show(Meeting $meeting)
    {
        $meeting->load([
            'material',
            'video',
            'postTest.questions',
            'postTest.attempts',
            'creator',
            'attendances' => function ($q) {
                $q->whereHas('user.roles', function ($r) {
                    $r->where('name', 'siswa');
                })->with('user');
            },
        ]);

        $attempt = null;

        // hanya hitung attempt kalau user login + siswa + ada post test
        if (
            auth()->check() &&
            auth()->user()->hasRole('siswa') &&
            $meeting->postTest
        ) {
            $attempt = $meeting->postTest
                ->attempts()
                ->where('user_id', auth()->id())
                ->first();
        }

        return view('meetings.show', compact('meeting', 'attempt'));
    }

    /**
     * Show create meeting form
     */
    public function create(Course $course)
    {
        return view('meetings.create', compact('course'));
    }

    /**
     * Store new meeting
     */
    public function store(Request $request, Course $course)
    {
        $request->validate([
            'title'     => 'required|string|max:255',
            'scheduled_at'  => 'required|date_format:Y-m-d\TH:i',
            'zoom_link' => 'nullable|url',
        ]);

        $meeting = Meeting::create([
            'course_id' => $course->id,
            'title'     => $request->title,
            'slug'      => Str::slug($request->title) . '-' . uniqid(),
            'scheduled_at'  => Carbon::createFromFormat(
                                'Y-m-d\TH:i',
                                $request->scheduled_at,
                                'Asia/Jakarta'
                        ),
            'zoom_link' => $request->zoom_link,
            'status'    => 'upcoming',
            'created_by'=> auth()->user()->id,
        ]);

        toast('success', 'Meeting berhasil dibuat');
        return redirect()->route('course.show', $course->slug);
    }
    public function edit(Meeting $meeting)
    {
        return view('meetings.edit', [
            'meeting' => $meeting,
            // format agar cocok dengan <input type="datetime-local">
            'scheduledAt' => optional($meeting->scheduled_at)
                ->timezone('Asia/Jakarta')
                ->format('Y-m-d\TH:i'),
        ]);
    }
    public function update(Request $request, Meeting $meeting)
    {
        $request->validate([
            'title'        => 'required|string|max:255',
            'scheduled_at' => 'required|date',
            'zoom_link'    => 'nullable|url',
        ]);

        $meeting->update([
            'title'        => $request->title,
            'slug'         => $meeting->slug
                                ?? Str::slug($request->title) . '-' . uniqid(),
            'scheduled_at' => Carbon::createFromFormat(
                'Y-m-d\TH:i',
                $request->scheduled_at,
                'Asia/Jakarta'
            ),
            'zoom_link'    => $request->zoom_link,
        ]);

        toast('success', 'Meeting berhasil diperbarui');

        return redirect()
            ->route('meeting.show', $meeting);
    }

    /**
     * Start meeting (tentor klik "Mulai")
     */
    public function start(Meeting $meeting)
    {
        $meeting->update([
            'status'   => 'live',
            'started_at' => now(),
        ]);
        toast('success', 'Meeting dimulai');
        return back();
    }

    /**
     * Finish meeting (tentor klik "Selesai")
     */
    public function finish(Meeting $meeting)
    {
        if ($meeting->status !== 'live') {
            abort(403, 'Meeting belum live');
        }

        $meeting->update([
            'status' => 'done',
        ]);
        toast('success', 'Meeting selesai');
        return back();
    }

    /**
     * Cancel meeting (optional)
     */
    public function cancel(Meeting $meeting)
    {
        if ($meeting->status === 'done') {
            abort(403, 'Meeting sudah selesai');
        }

        $meeting->update([
            'status' => 'cancelled',
        ]);
        toast('info', 'Meeting dibatalkan');
        return back();
    }

    /**
     * Delete meeting (soft delete)
     */
    public function destroy(Meeting $meeting)
    {
        // Cegah hapus jika masih punya relasi penting
        if (
            $meeting->material ||
            $meeting->video ||
            $meeting->postTest ||
            $meeting->attendances
        ) {
            toast(
                'error',
                'Meeting tidak dapat dihapus karena masih memiliki absensi, materi, video, atau post test.'
            );

            return back();
        }

        $meeting->delete();

        toast('warning', 'Meeting telah dihapus');

        return redirect()
            ->route('course.show', $meeting->course->slug);
    }

    public function joinZoom(Meeting $meeting)
    {
        // 1. Belum ada link zoom
        if (empty($meeting->zoom_link)) {
            toast('warning', 'Belum ada link Zoom untuk pertemuan ini');
            return back();
        }

        // 2. Hitung waktu join (30 menit sebelum jadwal)
        $scheduledAt = $meeting->scheduled_at->timezone('Asia/Jakarta');
        $joinAllowedAt = $scheduledAt->copy()->subMinutes(30);
        $now = Carbon::now('Asia/Jakarta');
        // 3. Belum waktunya join
        if ($now->lt($joinAllowedAt)) {
            toast(
                'error',
                'Tidak dapat join, tunggu hingga pukul ' .
                $joinAllowedAt->format('H:i') .
                ' WIB - Tanggal ' .
                $scheduledAt->format('d/m/Y')
            );

            return back();
        }

        // 4. Sudah boleh join
        return redirect()->away($meeting->zoom_link);
    }

}
