<?php

namespace App\Http\Controllers;

use App\Models\MeetingPostTest;
use App\Models\MeetingPostTestAttempt;
use App\Models\MeetingPostTestAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MeetingPostTestAttemptController extends Controller
{
    /**
     * Start attempt
     */
    public function start(MeetingPostTest $postTest)
    {

        if ($postTest->status !== 'active') {
            return back()->with('error', 'Post test belum aktif');
        }

        $attempt = MeetingPostTestAttempt::firstOrCreate(
            [
                'post_test_id' => $postTest->id,
                'user_id'      => auth()->user()->id,
            ],
            [
                'started_at' => now(),
            ]
        );

        return redirect()
            ->route('posttest.attempt.show', $attempt);
    }

    /**
     * Show attempt page
     */
    public function show(MeetingPostTestAttempt $attempt)
    {
        $attempt->load([
            'postTest.questions.question.options',
        ]);

        return view('posttests.attempt', compact('attempt'));
    }

    /**
     * Save answer (AJAX)
     */
    public function saveAnswer(Request $request, MeetingPostTestAttempt $attempt)
    {
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
     * Submit attempt (manual / auto)
     */
    public function submit(MeetingPostTestAttempt $attempt)
    {
        if ($attempt->is_submitted) {
            return back();
        }

        $attempt->load('postTest.questions.question.options');

        $scorePerQuestion = 5;
        $totalQuestions  = $attempt->postTest->questions->count();
        $correct         = 0;

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
            ? ($correct * $scorePerQuestion) / ($totalQuestions * $scorePerQuestion) * 100
            : 0;

        $attempt->update([
            'submitted_at'    => now(),
            'duration_seconds'=> now()->diffInSeconds($attempt->started_at),
            'score'           => round($score, 2),
            'is_submitted'    => true,
        ]);

        return redirect()
            ->route('posttest.result', $attempt);
    }

    /**
     * Show result
     */
    public function result(MeetingPostTestAttempt $attempt)
    {
        $attempt->load([
            'postTest.questions.question.options',
            'answers',
        ]);

        return view('posttests.result', compact('attempt'));
    }
}
