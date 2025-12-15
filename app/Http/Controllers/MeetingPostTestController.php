<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Models\MeetingPostTest;
use App\Models\MeetingPostTestQuestion;
use Illuminate\Http\Request;

class MeetingPostTestController extends Controller
{
    /**
     * Create post test for meeting
     */
    public function store(Request $request, Meeting $meeting)
    {
        $request->validate([
            'duration_minutes' => 'required|integer|min:1',
        ]);

        // satu meeting hanya boleh satu post test
        if ($meeting->postTest) {
            toast('error', 'Post test sudah ada');
            return back();
        }

        $postTest = MeetingPostTest::create([
            'meeting_id'       => $meeting->id,
            'duration_minutes' => $request->duration_minutes,
            'status'           => 'inactive',
        ]);

        toast('success', 'Post test berhasil dibuat');
        return redirect()->route('posttest.edit', $postTest);
    }

    /**
     * Edit post test (assign soal)
     */
    public function edit(MeetingPostTest $postTest)
    {
        $postTest->load([
            'meeting',
            'questions.question.options',
        ]);

        return view('posttests.edit', compact('postTest'));
    }

    /**
     * Attach questions to post test
     */
    public function attachQuestions(Request $request, MeetingPostTest $postTest)
    {
        $request->validate([
            'question_ids'   => 'required|array|min:1',
            'question_ids.*' => 'exists:questions,id',
        ]);

        // hapus dulu (reset urutan)
        $postTest->questions()->delete();

        foreach ($request->question_ids as $index => $questionId) {
            MeetingPostTestQuestion::create([
                'post_test_id' => $postTest->id,
                'question_id'  => $questionId,
                'order'        => $index + 1,
            ]);
        }
        toast('success', 'Soal berhasil disimpan');
        return back();
    }

    /**
     * Launch post test
     */
    public function launch(MeetingPostTest $postTest)
    {
        if ($postTest->questions()->count() === 0) {
            return back()->with('error', 'Minimal 1 soal');
        }

        $postTest->update([
            'status'      => 'active',
            'launched_at' => now(),
        ]);
        toast('success', 'Post test dimulai');
        return back();
    }

    /**
     * Close post test
     */
    public function close(MeetingPostTest $postTest)
    {
        $postTest->update([
            'status' => 'closed',
        ]);
        toast('info', 'Post test ditutup');
        return back();
    }
}
