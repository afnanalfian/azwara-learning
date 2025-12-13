<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\QuestionMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class QuestionController extends Controller
{
    public function index(Request $request, $material_id)
    {
        $material = QuestionMaterial::findOrFail($material_id);

        $query = Question::with('options')
            ->where('material_id', $material_id);

        // FILTER BY TYPE
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // SEARCH
        if ($request->filled('q')) {
            $query->where('question_text', 'like', '%' . $request->q . '%');
        }

        $questions = $query->latest()
            ->paginate(10)
            ->withQueryString();

        return view('bank.questions.index', compact('material', 'questions'));
    }

    public function create($material_id)
    {
        $material = QuestionMaterial::findOrFail($material_id);
        return view('bank.questions.create', compact('material'));
    }

    public function store(Request $request, $material_id)
    {
        // =========================
        // VALIDASI
        // =========================
        $request->validate([
            'type'          => 'required|in:mcq,mcma,truefalse',
            'question_text' => 'required',
            'explanation'   => 'nullable',
            'question_image'=> 'nullable|image|max:2048',
            'options'       => 'required_if:type,mcq,mcma|array|min:2',
        ]);

        // =========================
        // SIMPAN GAMBAR SOAL (JIKA ADA)
        // =========================
        $questionImage = null;
        if ($request->hasFile('question_image')) {
            $questionImage = $request->file('question_image')
                ->store('questions', 'public');
        }

        // =========================
        // SIMPAN SOAL
        // =========================
        $question = Question::create([
            'material_id'   => $material_id,
            'type'          => $request->type,
            'question_text' => $request->question_text,
            'image'         => $questionImage,
            'explanation'   => $request->explanation,
        ]);

        // =========================
        // MCQ & MCMA
        // =========================
        if (in_array($request->type, ['mcq', 'mcma'])) {

            // Normalisasi jawaban benar
            $correctIndexes = $request->type === 'mcq'
                ? [(int)$request->correct]   // radio → 1 index
                : ($request->correct ?? []); // checkbox → array

            foreach ($request->options as $i => $opt) {

                // upload gambar opsi (jika ada)
                $optionImage = null;
                if ($request->hasFile('option_images.' . $i)) {
                    $optionImage = $request->file('option_images.' . $i)
                        ->store('options', 'public');
                }

                QuestionOption::create([
                    'question_id' => $question->id,
                    'option_text' => $opt,
                    'image'       => $optionImage,
                    'is_correct'  => in_array($i, $correctIndexes),
                    'order'       => $i + 1,
                ]);
            }
        }

        // =========================
        // TRUE / FALSE
        // =========================
        if ($request->type === 'truefalse') {

            $isTrue = $request->truefalse_correct[0] ?? 1;

            QuestionOption::create([
                'question_id' => $question->id,
                'option_text' => 'Benar',
                'is_correct'  => $isTrue == 1,
                'order'       => 1,
            ]);

            QuestionOption::create([
                'question_id' => $question->id,
                'option_text' => 'Salah',
                'is_correct'  => $isTrue == 0,
                'order'       => 2,
            ]);
        }

        toast('success', 'Soal berhasil ditambahkan.');

        return redirect()->route('bank.material.questions.index', $material_id);
    }

    public function edit($id)
    {
        $question = Question::with('options')->findOrFail($id);
        return view('bank.questions.edit', compact('question'));
    }

    public function update(Request $request, $id)
    {
        $question = Question::with('options')->findOrFail($id);

        // =========================
        // VALIDASI
        // =========================
        $request->validate([
            'type'          => 'required|in:mcq,mcma,truefalse',
            'question_text' => 'required',
            'explanation'   => 'nullable',
            'question_image'=> 'nullable|image|max:2048',
            'options'       => 'required_if:type,mcq,mcma|array|min:2',
        ]);

        // =========================
        // UPDATE GAMBAR SOAL
        // =========================
        $questionImage = $question->image;

        if ($request->hasFile('question_image')) {
            $questionImage = $request->file('question_image')
                ->store('questions', 'public');
        }

        // =========================
        // UPDATE SOAL
        // =========================
        $question->update([
            'type'          => $request->type,
            'question_text' => $request->question_text,
            'image'         => $questionImage,
            'explanation'   => $request->explanation,
        ]);

        // =========================
        // RESET OPSI LAMA
        // =========================
        $question->options()->delete();

        // =========================
        // MCQ & MCMA
        // =========================
        if (in_array($request->type, ['mcq', 'mcma'])) {

            $correctIndexes = $request->type === 'mcq'
                ? [(int)$request->correct]
                : ($request->correct ?? []);

            foreach ($request->options as $i => $opt) {

                $optionImage = null;
                if ($request->hasFile('option_images.' . $i)) {
                    $optionImage = $request->file('option_images.' . $i)
                        ->store('options', 'public');
                }
                if ($request->hasFile('question_image')) {
                    Storage::disk('public')->delete($question->image);
                }

                QuestionOption::create([
                    'question_id' => $question->id,
                    'option_text' => $opt,
                    'image'       => $optionImage,
                    'is_correct'  => in_array($i, $correctIndexes),
                    'order'       => $i + 1,
                ]);
            }
        }

        // =========================
        // TRUE / FALSE
        // =========================
        if ($request->type === 'truefalse') {

            $isTrue = $request->truefalse_correct[0] ?? 1;

            QuestionOption::create([
                'question_id' => $question->id,
                'option_text' => 'Benar',
                'is_correct'  => $isTrue == 1,
                'order'       => 1,
            ]);

            QuestionOption::create([
                'question_id' => $question->id,
                'option_text' => 'Salah',
                'is_correct'  => $isTrue == 0,
                'order'       => 2,
            ]);
        }

        toast('success', 'Soal berhasil diperbarui.');

        return redirect()->route('bank.material.questions.index', $question->material_id);
    }

    public function destroy($id)
    {
        $question = Question::with('options')->findOrFail($id);
        $material_id = $question->material_id;

        // =========================
        // HAPUS FILE GAMBAR (OPTIONAL TAPI DIREKOMENDASI)
        // =========================

        // gambar soal
        if ($question->image) {
            Storage::disk('public')->delete($question->image);
        }

        // gambar opsi
        foreach ($question->options as $option) {
            if ($option->image) {
                Storage::disk('public')->delete($option->image);
            }
        }

        // =========================
        // SOFT DELETE OPSI
        // =========================
        $question->options()->delete();

        // =========================
        // SOFT DELETE SOAL
        // =========================
        $question->delete();

        toast('warning', 'Soal telah dihapus.');

        return redirect()->route('bank.material.questions.index', $material_id);
    }
}
