<?php

namespace App\Http\Controllers;

use App\Models\QuestionMaterial;
use App\Models\QuestionCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class QuestionMaterialController extends Controller
{
    public function index($category_id)
    {
        $category = QuestionCategory::findOrFail($category_id);
        $materials = $category->materials()->paginate(15);

        return view('bank.material.index', compact('category', 'materials'));
    }

    public function create($category_id)
    {
        $category = QuestionCategory::findOrFail($category_id);
        return view('bank.material.create', compact('category'));
    }

    public function store(Request $request, $category_id)
    {
        $request->validate([
            'name' => 'required|max:255',
        ]);

        QuestionMaterial::create([
            'category_id' => $category_id,
            'name'        => $request->name,
            'slug'        => Str::slug($request->name),
        ]);

        toast('success','Materi berhasil ditambahkan.');

        return redirect()->route('bank.category.materials.index', $category_id);
    }

    public function edit($id)
    {
        $material  = QuestionMaterial::findOrFail($id);
        $category  = $material->category;

        return view('bank.material.edit', compact('material', 'category'));
    }

    public function update(Request $request, $id)
    {
        $material = QuestionMaterial::findOrFail($id);

        $request->validate([
            'name' => 'required|max:255',
        ]);

        $data = $request->only('name');

        if ($request->name !== $material->name) {
            $data['slug'] = Str::slug($request->name);
        }

        $material->update($data);

        toast('success','Materi berhasil diupdate.');

        return redirect()->route('bank.category.materials.index', $material->category_id);
    }

    public function destroy($id)
    {
        $material = QuestionMaterial::findOrFail($id);

        if ($material->questions()->exists()) {
            toast('error','Materi tidak bisa dihapus, masih ada soal.');
            return back();
        }

        $category_id = $material->category_id;
        $material->delete();

        toast('warning','Materi berhasil dihapus.');

        return redirect()->route('bank.category.materials.index', $category_id);
    }
}
