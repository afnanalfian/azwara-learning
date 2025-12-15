<?php

namespace App\Http\Controllers;

use App\Models\QuestionCategory;
use Illuminate\Http\Request;

class QuestionCategoryController extends Controller
{
    public function index()
    {
        $categories = QuestionCategory::latest()->paginate(12);
        return view('bank.category.index', compact('categories'));
    }

    public function create()
    {
        return view('bank.category.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|max:255',
            'thumbnail'   => 'nullable|image',
            'description' => 'nullable',
        ]);

        $data = $request->only('name','description');
        $data['slug'] = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $request->name));

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')
                ->store('bank/category', 'public');
        }

        QuestionCategory::create($data);

        toast('success','Kategori berhasil dibuat.');

        return redirect()->route('bank.category.index');
    }

    public function edit(QuestionCategory $category)
    {
        return view('bank.category.edit', compact('category'));
    }

    public function update(Request $request, QuestionCategory $category)
    {
        $request->validate([
            'name'        => 'required|max:255',
            'thumbnail'   => 'nullable|image',
            'description' => 'nullable',
        ]);

        $data = $request->only('name','description');

        if ($request->name !== $category->name) {
            $data['slug'] = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $request->name));
        }

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')
                ->store('bank/category', 'public');
        }

        $category->update($data);

        toast('success','Kategori berhasil diupdate.');

        return redirect()->route('bank.category.index');
    }

    public function destroy(QuestionCategory $category)
    {
        if ($category->materials()->exists()) {
            toast('error','Kategori tidak bisa dihapus, masih ada materi.');
            return back();
        }

        $category->delete();

        toast('warning','Kategori telah dihapus.');

        return back();
    }
}
