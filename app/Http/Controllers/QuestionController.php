<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\QuestionMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class QuestionController extends Controller
{
    public function index(Request $request, QuestionMaterial $material)
    {
        $query = Question::with('options')
            ->where('material_id', $material->id);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('q')) {
            $query->where('question_text', 'like', '%' . $request->q . '%');
        }

        $questions = $query->latest()
            ->paginate(10)
            ->withQueryString();

        return view('bank.questions.index', compact('material', 'questions'));
    }

    public function create(QuestionMaterial $material)
    {
        return view('bank.questions.create', compact('material'));
    }

    public function store(Request $request, QuestionMaterial $material)
    {
        $request->validate([
            'type'          => 'required|in:mcq,mcma,truefalse',
            'question_text' => 'required',
            'explanation'   => 'nullable',
            'question_image'=> 'nullable|image|max:2048',
            'options'       => 'required_if:type,mcq,mcma|array|min:2',
        ]);

        $questionImage = null;
        if ($request->hasFile('question_image')) {
            $questionImage = $request->file('question_image')
                ->store('questions', 'public');
        }

        $question = Question::create([
            'material_id'   => $material->id,
            'type'          => $request->type,
            'question_text' => $request->question_text,
            'image'         => $questionImage,
            'explanation'   => $request->explanation,
        ]);

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

                QuestionOption::create([
                    'question_id' => $question->id,
                    'option_text' => $opt,
                    'image'       => $optionImage,
                    'is_correct'  => in_array($i, $correctIndexes),
                    'order'       => $i + 1,
                ]);
            }
        }

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

        return redirect()->route('bank.material.questions.index', $material);
    }

    public function edit(Question $question)
    {
        $question->load('options');
        return view('bank.questions.edit', compact('question'));
    }

    public function update(Request $request, Question $question)
    {
        $request->validate([
            'type'          => 'required|in:mcq,mcma,truefalse',
            'question_text' => 'required',
            'explanation'   => 'nullable',
            'question_image'=> 'nullable|image|max:2048',
            'options'       => 'required_if:type,mcq,mcma|array|min:2',
        ]);

        $questionImage = $question->image;

        if ($request->hasFile('question_image')) {
            if ($question->image) {
                Storage::disk('public')->delete($question->image);
            }
            $questionImage = $request->file('question_image')
                ->store('questions', 'public');
        }

        $question->update([
            'type'          => $request->type,
            'question_text' => $request->question_text,
            'image'         => $questionImage,
            'explanation'   => $request->explanation,
        ]);

        foreach ($question->options as $opt) {
            if ($opt->image) {
                Storage::disk('public')->delete($opt->image);
            }
        }

        $question->options()->delete();

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

                QuestionOption::create([
                    'question_id' => $question->id,
                    'option_text' => $opt,
                    'image'       => $optionImage,
                    'is_correct'  => in_array($i, $correctIndexes),
                    'order'       => $i + 1,
                ]);
            }
        }

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

        return redirect()->route('bank.material.questions.index', $question->material);
    }

    public function destroy(Question $question)
    {
        $material = $question->material;

        if ($question->image) {
            Storage::disk('public')->delete($question->image);
        }

        foreach ($question->options as $option) {
            if ($option->image) {
                Storage::disk('public')->delete($option->image);
            }
        }

        $question->options()->delete();
        $question->delete();

        toast('warning', 'Soal telah dihapus.');

        return redirect()->route('bank.material.questions.index', $material);
    }
}
