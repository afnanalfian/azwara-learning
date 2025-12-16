<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Models\MeetingPostTest;
use App\Models\MeetingPostTestQuestion;
use App\Models\QuestionCategory;
use App\Models\Question;
use App\Models\QuestionMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MeetingPostTestController extends Controller
{
    /**
     * Store post test
     */
    public function store(Request $request, Meeting $meeting)
    {
        if ($meeting->postTest) {
            toast('error', 'Post test sudah ada');
            return back();
        }

        $postTest = MeetingPostTest::create([
            'meeting_id'       => $meeting->id,
            'duration_minutes' => null,
            'status'           => 'inactive',
        ]);

        toast('success', 'Post test berhasil dibuat');
        return redirect()->route('posttest.edit', $postTest);
    }

    /**
     * Edit post test
     */
    public function edit(MeetingPostTest $postTest)
    {
        // Load relasi utama
        $postTest->load([
            'meeting.course',
            'questions.question.options',
        ]);

        // Kategori + materi (untuk filter)
        $categories = QuestionCategory::with([
            'materials' => function ($q) {
                $q->orderBy('name');
            }
        ])->orderBy('name')->get();

        // ID soal yang sudah dipakai (important!)
        $usedQuestionIds = $postTest->questions
            ->pluck('question_id')
            ->toArray();

        return view('meetings.posttests.edit', [
            'postTest'         => $postTest,
            'categories'       => $categories,
            'usedQuestionIds'  => $usedQuestionIds,
        ]);
    }

    public function updateDuration(Request $request, MeetingPostTest $postTest)
    {
        if ($postTest->status !== 'inactive') {
            toast('error', 'Durasi tidak bisa diubah karena post test sudah berjalan / ditutup');
            return back();
        }

        // validasi
        $request->validate([
            'duration_minutes' => ['required', 'integer', 'min:1'],
        ]);

        // update
        $postTest->update([
            'duration_minutes' => $request->duration_minutes,
        ]);

        toast('success', 'Durasi post test berhasil diperbarui');
        return back();
    }
    public function questionsByMaterial(
        Request $request,
        MeetingPostTest $postTest,
        QuestionMaterial $material
    ) {

        // Soal yang sudah dipakai
        $usedQuestionIds = $postTest->questions()
            ->pluck('question_id')
            ->toArray();

        // Query utama
        $query = Question::with('options')
            ->where('material_id', $material->id)
            ->whereNotIn('id', $usedQuestionIds);

        // FILTER TIPE SOAL
        if ($request->filled('type')) {
            $request->validate([
                'type' => 'in:mcq,mcma,truefalse',
            ]);

            $query->where('type', $request->type);
        }

        // Pagination server-side
        $questions = $query
            ->latest()
            ->paginate(10)
            ->withQueryString(); // ← penting utk pagination + filter

        return response()->json($questions);
    }

    /**
     * Attach questions
     */
    public function attachQuestions(Request $request, MeetingPostTest $postTest)
    {
        if ($postTest->status !== 'inactive') {
            toast('error', 'Post test sudah aktif / ditutup');
            return back();
        }

        $request->validate([
            'question_ids'   => ['required', 'array', 'min:1'],
            'question_ids.*' => ['exists:questions,id'],
        ]);

        DB::transaction(function () use ($request, $postTest) {

            // Ambil order terakhir
            $lastOrder = $postTest->questions()->max('order') ?? 0;

            foreach ($request->question_ids as $index => $questionId) {

                // Hindari duplicate
                $exists = $postTest->questions()
                    ->where('question_id', $questionId)
                    ->exists();

                if (!$exists) {
                    MeetingPostTestQuestion::create([
                        'post_test_id' => $postTest->id,
                        'question_id'  => $questionId,
                        'order'        => $lastOrder + $index + 1,
                    ]);
                }
            }
        });

        toast('success', 'Soal berhasil ditambahkan');
        return back();
    }
    public function detachQuestion(
        MeetingPostTest $postTest,
        Question $question
    ) {
        if ($postTest->status !== 'inactive') {
            toast('error', 'Post test tidak bisa diubah');
            return back();
        }

        $postTest->questions()
            ->where('question_id', $question->id)
            ->delete();

        // rapikan order
        $postTest->questions()
            ->orderBy('order')
            ->get()
            ->each(fn ($q, $i) =>
                $q->update(['order' => $i + 1])
            );

        toast('success', 'Soal dihapus');
        return back();
    }

    /**
     * Launch post test
     */
    public function launch(MeetingPostTest $postTest)
    {
        if ($postTest->status !== 'inactive') {
            toast('error', 'Post test sudah aktif / ditutup');
            return back();
        }

        if ($postTest->questions()->count() === 0) {
            toast('error', 'Minimal 1 soal');
            return back();
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
        if ($postTest->status !== 'active') {
            toast('error', 'Post test belum aktif');
            return back();
        }

        $postTest->update([
            'status' => 'closed',
        ]);

        toast('info', 'Post test ditutup');
        return back();
    }
    public function resultAdmin(MeetingPostTest $postTest)
    {
        $postTest->load([
            'meeting',
            'questions.question.options',
            'attempts.user',
            'attempts.answers',
        ]);

        // Ranking
        $attempts = $postTest->attempts
            ->where('is_submitted', true)
            ->sortByDesc('score')
            ->values();

        // Statistik per soal
        $questionStats = [];

        foreach ($postTest->questions as $pq) {
            $questionId = $pq->question_id;

            $total = $attempts->count();

            $correct = $attempts->filter(function ($attempt) use ($questionId) {
                return optional(
                    $attempt->answers->where('question_id', $questionId)->first()
                )->is_correct === true;
            })->count();

            $questionStats[$questionId] = [
                'total'   => $total,
                'correct' => $correct,
            ];
        }

        return view('meetings.posttests.result-admin', compact(
            'postTest',
            'attempts',
            'questionStats'
        ));
    }

}
