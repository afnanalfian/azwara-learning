<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Models\MeetingAttendance;
use Illuminate\Http\Request;

class MeetingAttendanceController extends Controller
{
    /**
     * Show attendance page
     */
    public function index(Meeting $meeting)
    {
        // ambil siswa dari course
        $students = $meeting->course
            ->purchases()
            ->where('status', 'paid')
            ->with('user')
            ->get()
            ->pluck('user');

        // attendance existing
        $attendance = MeetingAttendance::where('meeting_id', $meeting->id)
            ->get()
            ->keyBy('user_id');

        return view('meetings.attendance', compact(
            'meeting',
            'students',
            'attendance'
        ));
    }

    /**
     * Save attendance
     */
    public function store(Request $request, Meeting $meeting)
    {
        $request->validate([
            'attendances' => 'array',
        ]);

        foreach ($request->attendances ?? [] as $userId => $isPresent) {
            MeetingAttendance::updateOrCreate(
                [
                    'meeting_id' => $meeting->id,
                    'user_id'    => $userId,
                ],
                [
                    'is_present' => (bool) $isPresent,
                    'marked_by'  => auth()->user()->id,
                    'marked_at'  => now(),
                ]
            );
        }
        toast('success', 'Absensi berhasil disimpan');
        return back();
    }
}
