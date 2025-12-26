<?php

namespace App\Http\Controllers\Course;

use App\Models\Course;
use App\Models\Meeting;
use App\Models\Exam;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Http\Controllers\Controller;

class MeetingController extends Controller
{
    /**
     * Show meeting detail
     * (materi, video, attendance, post test)
     */
    public function show(Meeting $meeting)
    {
        // ===============================
        // AUTHORIZATION CHECK (CUSTOM)
        // ===============================
        if (
            auth()->check() &&
            auth()->user()->hasRole('siswa') &&
            auth()->user()->cannot('view', $meeting)
        ) {
            toast('error', 'Silakan lakukan pembelian terlebih dahulu');
            return redirect()->back();
        }

        // ===============================
        // LOAD RELATIONS
        // ===============================
        $meeting->load([
            'material',
            'video',
            'exam.questions',
            'exam.attempts',
            'creator',
            'attendances' => function ($q) {
                $q->whereHas('user.roles', function ($r) {
                    $r->where('name', 'siswa');
                })->with('user');
            },
        ]);

        $attempt = null;

        // ===============================
        // HITUNG ATTEMPT (KHUSUS SISWA)
        // ===============================
        if (
            auth()->check() &&
            auth()->user()->hasRole('siswa') &&
            $meeting->exam
        ) {
            $attempt = $meeting->exam
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
            'title'        => 'required|string|max:255',
            'scheduled_at' => 'required|date_format:Y-m-d\TH:i',
            'zoom_link'    => 'nullable|url',
        ]);

        $meeting = Meeting::create([
            'course_id'    => $course->id,
            'title'        => $request->title,
            'slug'         => Str::slug($request->title) . '-' . uniqid(),
            'scheduled_at' => Carbon::createFromFormat(
                'Y-m-d\TH:i',
                $request->scheduled_at,
                'Asia/Jakarta'
            ),
            'zoom_link'    => $request->zoom_link,
            'status'       => 'upcoming',
            'created_by'   => auth()->id(),
        ]);

        toast('success', 'Meeting berhasil dibuat');
        return redirect()->route('course.show', $course->slug);
    }

    public function edit(Meeting $meeting)
    {
        return view('meetings.edit', [
            'meeting'     => $meeting,
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

        return redirect()->route('meeting.show', $meeting);
    }

    /**
     * Start meeting
     */
    public function start(Meeting $meeting)
    {
        $meeting->update([
            'status'     => 'live',
            'started_at' => now(),
        ]);

        toast('success', 'Meeting dimulai');
        return back();
    }

    /**
     * Finish meeting
     */
    public function finish(Meeting $meeting)
    {
        if ($meeting->status !== 'live') {
            abort(403, 'Meeting belum live');
        }

        $meeting->update([
            'status' => 'done',
        ]);
        /** NOTIFY TEACHERS */
        foreach ($meeting->course->teachers as $teacher) {
            notify_user(
                $teacher->user,
                "Meeting '{$meeting->title}' telah selesai. Harap upload materi dan video.",
                false,
                route('meeting.show', $meeting)
            );
        }
        toast('success', 'Meeting selesai');
        return back();
    }

    /**
     * Delete meeting
     */
    public function destroy(Meeting $meeting)
    {
        if (
            $meeting->material ||
            $meeting->video ||
            $meeting->exam ||   // ⬅️ GANTI DI SINI
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

    /**
     * Join Zoom
     */
    public function joinZoom(Meeting $meeting)
    {
        if (empty($meeting->zoom_link)) {
            toast('warning', 'Belum ada link Zoom untuk pertemuan ini');
            return back();
        }

        $scheduledAt = $meeting->scheduled_at->timezone('Asia/Jakarta');
        $joinAllowedAt = $scheduledAt->copy()->subMinutes(30);
        $now = Carbon::now('Asia/Jakarta');

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

        return redirect()->away($meeting->zoom_link);
    }

    /**
     * ===============================
     * POST TEST (EXAM) CREATOR
     * ===============================
     */
    public function storePostTest(Meeting $meeting)
    {
        if ($meeting->exam) {
            toast('error', 'Post Test sudah ada');
            return back();
        }

        $exam = Exam::create([
            'type'         => 'post_test',
            'status'       => 'inactive',
            'owner_type'   => Meeting::class,
            'owner_id'     => $meeting->id,
            'created_by'   => auth()->id(),
        ]);

        toast('success', 'Post Test berhasil dibuat');

        return redirect()->route('exams.edit', $exam);
    }
}
