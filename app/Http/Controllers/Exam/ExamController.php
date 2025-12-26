<?php

namespace App\Http\Controllers\Exam;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Meeting;
use App\Models\QuestionCategory;
use Illuminate\Http\Request;
use App\Models\User;

class ExamController extends Controller
{
    /* ================= LIST ================= */

    public function index()
    {
        $exams = Exam::latest()->paginate(15);
        return view('exams.index', compact('exams'));
    }
    public function show(Exam $exam)
    {
        $exam->load([
            'questions',
            'attempts',
        ]);

        $attempt = null;

        if (
            auth()->check() &&
            auth()->user()->hasRole('siswa')
        ) {
            $attempt = $exam->attempts()
                ->where('user_id', auth()->id())
                ->latest()
                ->first();
        }

        return view('exams.show', compact('exam', 'attempt'));
    }

    public function indexTryout(Request $request)
    {
        $exams = Exam::where('type', 'tryout')
            ->when($request->q, fn ($q) =>
                $q->where('title', 'like', "%{$request->q}%")
            )
            ->when($request->date, fn ($q) =>
                $q->whereDate('exam_date', $request->date)
            )
            ->latest('exam_date')
            ->paginate(10);

        return view('exams.tryout.index', compact('exams'));
    }
    public function indexQuiz(Request $request)
    {
        $exams = Exam::where('type', 'quiz')
            ->when($request->q, fn ($q) =>
                $q->where('title', 'like', "%{$request->q}%")
            )
            ->when($request->date, fn ($q) =>
                $q->whereDate('exam_date', $request->date)
            )
            ->latest('exam_date')
            ->paginate(10);

        return view('exams.quiz.index', compact('exams'));
    }

    /* ================= CREATE ================= */

    public function create(Request $request)
    {
        // untuk tryout / daily quiz
        $type = $request->get('type', 'tryout');

        return view('exams.create', compact('type'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|in:quiz,tryout',
            'title' => 'required|string|max:255',
            'exam_date' => 'required|date',
        ]);

        $exam = Exam::create([
            'type' => $data['type'],
            'title' => $data['title'],
            'exam_date' => $data['exam_date'],
            'status' => 'inactive',
            'created_by' => auth()->id(),
        ]);

        toast('success', ucfirst($data['type']) . ' berhasil dibuat');

        return redirect()->route('exams.edit', $exam);
    }

    /* ================= EDIT ================= */
    public function edit(Exam $exam)
    {
        // Load soal yang sudah dipilih
        $exam->load([
            'questions.question.options',
        ]);

        // Ambil ID soal yang sudah dipakai
        $usedQuestionIds = $exam->questions
            ->pluck('question_id')
            ->toArray();

        // Load kategori + materi (UNTUK MODAL PICKER)
        $categories = QuestionCategory::with([
            'materials' => function ($q) {
                $q->orderBy('name');
            }
        ])
        ->orderBy('name')
        ->get();

        return view('exams.edit', compact(
            'exam',
            'categories',
            'usedQuestionIds'
        ));
    }
    public function update(Request $request, Exam $exam)
    {
        if ($exam->status !== 'inactive') {
            abort(403, 'Exam sedang berjalan');
        }

        $exam->update($request->only([
            'title',
            'duration_minutes',
        ]));

        toast('success', 'Exam diperbarui');
        return back();
    }

    /* ================= STATUS ================= */

    public function activate(Exam $exam)
    {
        $exam->update(['status' => 'active']);
        /** TARGET USERS */
        $users = collect();

        if (in_array($exam->type, ['tryout', 'quiz'])) {
            $users = User::whereHas('entitlements', function ($q) use ($exam) {
                $q->where('entitlement_type', $exam->type);
            })->get();
        }

        if ($exam->type === 'post_test' && $exam->owner_type === Meeting::class) {
            $meeting = Meeting::find($exam->owner_id);
            $users   = $this->usersWithMeetingAccess($meeting);
        }

        foreach ($users as $user) {
            notify_user(
                $user,
                "Ujian '{$exam->title}' telah dibuka. Silakan dikerjakan.",
                false,
                route('exams.show', $exam)
            );
        }
        return back()->with('success', 'Ujian diaktifkan');
    }

    public function close(Exam $exam)
    {
        $exam->update(['status' => 'closed']);

        return back()->with('success', 'Ujian ditutup');
    }
    public function destroy(Exam $exam)
    {
        // Cegah hapus kalau sudah ada attempt
        if ($exam->attempts()->exists()) {
            toast('error', 'Ujian sudah dikerjakan, tidak dapat dihapus');
            return back();
        }

        // Kalau post test, pastikan meeting masih ada
        if (
            $exam->examable_type === Meeting::class &&
            !$exam->examable
        ) {
            toast('error', 'Meeting tidak ditemukan');
            return back();
        }

        $exam->delete();

        toast('success', 'Ujian berhasil dihapus');

        // Redirect sesuai tipe
        if ($exam->examable_type === Meeting::class) {
            return redirect()
                ->route('meeting.show', $exam->examable);
        }

        return redirect()
            ->route(
                $exam->type === 'quiz'
                    ? 'quizzes.index'
                    : 'tryouts.index'
            );
    }

}
