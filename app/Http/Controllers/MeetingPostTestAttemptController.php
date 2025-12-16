<?php

namespace App\Http\Controllers;

use App\Models\MeetingPostTest;
use App\Models\MeetingPostTestAttempt;
use App\Models\MeetingPostTestAnswer;
use Illuminate\Http\Request;

class MeetingPostTestAttemptController extends Controller
{
    /**
     * Start attempt
     */
    public function start(MeetingPostTest $postTest)
    {
        if ($postTest->status !== 'active') {
            toast('error', 'Post test belum aktif');
            return back();
        }

        $attempt = MeetingPostTestAttempt::firstOrCreate(
            [
                'post_test_id' => $postTest->id,
                'user_id'      => auth()->id(),
            ],
            [
                'started_at' => now(),
            ]
        );

        return redirect()->route('posttest.attempt.show', $attempt);
    }

    /**
     * Show attempt
     */
    public function show(MeetingPostTestAttempt $attempt)
    {
        abort_if($attempt->user_id !== auth()->id(), 403);

        if ($attempt->is_submitted) {
            return redirect()->route('posttest.result', $attempt);
        }

        if ($attempt->postTest->status !== 'active') {
            toast('error', 'Post test sudah ditutup');
            return redirect()->route('meeting.show', $attempt->postTest->meeting);
        }
        $attempt->load([
            'postTest.questions.question.options',
            'answers',
        ]);

        return view('meetings.posttests.attempt', compact('attempt'));
    }

    /**
     * Save answer (AJAX)
     */
    public function saveAnswer(Request $request, MeetingPostTestAttempt $attempt)
    {
        abort_if($attempt->user_id !== auth()->id(), 403);

        if ($attempt->is_submitted) {
            return response()->json(['status' => 'locked'], 403);
        }

        $request->validate([
            'question_id'      => 'required|exists:questions,id',
            'selected_options' => 'nullable|array',
        ]);

        MeetingPostTestAnswer::updateOrCreate(
            [
                'attempt_id'  => $attempt->id,
                'question_id' => $request->question_id,
            ],
            [
                'selected_options' => $request->selected_options ?? [],
            ]
        );

        return response()->json(['status' => 'ok']);
    }

    /**
     * Submit attempt
     */
    public function submit(MeetingPostTestAttempt $attempt)
    {
        abort_if($attempt->user_id !== auth()->id(), 403);

        if ($attempt->is_submitted) {
            return redirect()->route('posttest.result', $attempt);
        }

        $attempt->load('postTest.questions.question.options');

        $totalQuestions = $attempt->postTest->questions->count();
        $correct = 0;

        foreach ($attempt->postTest->questions as $pq) {
            $question = $pq->question;

            $answer = $attempt->answers()
                ->where('question_id', $question->id)
                ->first();

            $correctOptions = $question->options
                ->where('is_correct', true)
                ->pluck('id')
                ->sort()
                ->values()
                ->toArray();

            $selected = $answer
                ? collect($answer->selected_options)->sort()->values()->toArray()
                : [];

            $isCorrect = $correctOptions === $selected;

            if ($answer) {
                $answer->update(['is_correct' => $isCorrect]);
            }

            if ($isCorrect) {
                $correct++;
            }
        }

        $score = $totalQuestions > 0
            ? round(($correct / $totalQuestions) * 100, 2)
            : 0;

        $attempt->update([
            'submitted_at'     => now(),
            'duration_seconds' => now()->diffInSeconds($attempt->started_at),
            'score'            => $score,
            'is_submitted'     => true,
        ]);

        return redirect()->route('posttest.result', $attempt);
    }

    /**
     * Result
     */
    public function result(MeetingPostTestAttempt $attempt)
    {
        $attempt->load([
            'postTest.questions.question.options',
            'answers',
            'user',
        ]);

        return view('meetings.posttests.result', compact('attempt'));
    }
}
