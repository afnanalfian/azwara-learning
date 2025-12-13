<?php

namespace App\Http\Controllers;

use App\Models\QuestionCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
        $data['slug'] = Str::slug($request->name);

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')
                ->store('bank/category', 'public');
        }

        QuestionCategory::create($data);

        toast('success','Kategori berhasil dibuat.');

        return redirect()->route('bank.category.index');
    }

    public function edit($id)
    {
        $category = QuestionCategory::findOrFail($id);
        return view('bank.category.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $category = QuestionCategory::findOrFail($id);

        $request->validate([
            'name'        => 'required|max:255',
            'thumbnail'   => 'nullable|image',
            'description' => 'nullable',
        ]);

        $data = $request->only('name','description');

        // Update slug only if changed
        if ($request->name !== $category->name) {
            $data['slug'] = Str::slug($request->name);
        }

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')
                ->store('bank/category', 'public');
        }

        $category->update($data);

        toast('success','Kategori berhasil diupdate.');

        return redirect()->route('bank.category.index');
    }

    public function destroy($id)
    {
        $category = QuestionCategory::findOrFail($id);

        if ($category->materials()->exists()) {
            toast('error','Kategori tidak bisa dihapus, masih ada materi.');
            return back();
        }

        $category->delete();

        toast('warning','Kategori telah dihapus.');

        return redirect()->route('bank.category.index');
    }
}
