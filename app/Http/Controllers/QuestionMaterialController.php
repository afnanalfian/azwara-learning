<?php

namespace App\Http\Controllers;

use App\Models\QuestionMaterial;
use App\Models\QuestionCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class QuestionMaterialController extends Controller
{
    public function index(QuestionCategory $category)
    {
        $materials = $category->materials()->paginate(15);
        return view('bank.material.index', compact('category', 'materials'));
    }

    public function create(QuestionCategory $category)
    {
        return view('bank.material.create', compact('category'));
    }

    public function store(Request $request, QuestionCategory $category)
    {
        $request->validate([
            'name' => 'required|max:255',
        ]);

        QuestionMaterial::create([
            'category_id' => $category->id,
            'name'        => $request->name,
            'slug'        => strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $request->name)),
        ]);

        toast('success','Materi berhasil ditambahkan.');

        return redirect()->route('bank.category.materials.index', $category);
    }

    public function edit(QuestionMaterial $material)
    {
        $category = $material->category;

        return view('bank.material.edit', compact('material', 'category'));
    }

    public function update(Request $request, QuestionMaterial $material)
    {
        $request->validate([
            'name' => 'required|max:255',
        ]);

        $data['name'] = $request->name;

        if ($request->name !== $material->name) {
            $data['slug'] = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $request->name));
        }

        $material->update($data);

        toast('success','Materi berhasil diupdate.');

        return redirect()->route('bank.category.materials.index', $material->category);
    }

    public function destroy(QuestionMaterial $material)
    {
        if ($material->questions()->exists()) {
            toast('error','Materi tidak bisa dihapus, masih ada soal.');
            return back();
        }

        $category = $material->category;
        $material->delete();

        toast('warning','Materi berhasil dihapus.');

        return redirect()->route('bank.category.materials.index', $category);
    }
}

