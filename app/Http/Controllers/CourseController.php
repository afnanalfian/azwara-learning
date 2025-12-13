<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->query('q');

        $courses = Course::with('teachers.user')
            ->when($q, fn($qr) => $qr->where('name','like',"%{$q}%"))
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('course.index', compact('courses','q'));
    }

    public function create()
    {
        return view('course.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|max:255',
            'slug'        => 'required|max:255|unique:courses,slug',
            'description' => 'required|max:1000',
            'thumbnail'   => 'required|image',
        ]);

        $data = $request->only('name','slug','description');
        $data['thumbnail'] = $request->file('thumbnail')->store('courses','public');

        Course::create($data);

        toast('success','Course berhasil dibuat.');

        return redirect()->route('course.index');
    }

    public function show($slug)
    {
        $course = Course::where('slug',$slug)
                ->with(['teachers.user','meetings'])
                ->firstOrFail();

        return view('course.show', compact('course'));
    }

    public function edit($slug)
    {
        $course = Course::where('slug', $slug)->firstOrFail();

        return view('course.edit', compact('course'));
    }

    public function update(Request $request, $slug)
    {
        $course = Course::where('slug', $slug)->firstOrFail();

        $request->validate([
            'name'        => 'required|max:255',
            'slug'        => 'required|max:255|unique:courses,slug,' . $course->id,
            'description' => 'required|max:1000',
            'thumbnail'   => 'nullable|image',
        ]);

        $data = $request->only('name','slug','description');

        if ($request->hasFile('thumbnail')) {

            if ($course->thumbnail && file_exists(storage_path('app/public/'.$course->thumbnail))) {
                unlink(storage_path('app/public/'.$course->thumbnail));
            }

            $data['thumbnail'] = $request->file('thumbnail')->store('courses','public');
        }

        $course->update($data);

        toast('info','Course berhasil diperbarui.');

        return redirect()->route('course.index');
    }

    public function destroy($slug)
    {
        $course = Course::where('slug', $slug)->firstOrFail();

        if ($course->thumbnail && file_exists(storage_path('app/public/'.$course->thumbnail))) {
            unlink(storage_path('app/public/'.$course->thumbnail));
        }

        foreach ($course->meetings as $meeting) {

            foreach ($meeting->materials as $m) {
                $m->delete();
            }

            if ($meeting->postTest) {
                $meeting->postTest->delete();
            }

            if ($meeting->video) {
                $meeting->video->delete();
            }

            $meeting->delete();
        }

        $course->delete();

        toast('warning','Course telah dihapus.');

        return redirect()->route('course.index');
    }
}
