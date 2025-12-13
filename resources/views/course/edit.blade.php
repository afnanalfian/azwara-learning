@extends('layouts.app')

@section('content')

<div class="max-w-3xl mx-auto px-4 py-8">

    {{-- TITLE --}}
    <h1 class="text-2xl font-bold text-azwara-darker dark:text-azwara-lighter mb-6">
        Edit Course
    </h1>

    <form action="{{ route('course.update', $course->slug) }}"
          method="POST"
          enctype="multipart/form-data"
          class="space-y-6">

        @csrf

        {{-- NAME --}}
        <div>
            <label class="block font-semibold text-sm text-gray-700 dark:text-gray-200">
                Nama Course <span class="text-red-500">*</span>
            </label>

            <input type="text"
                   name="name"
                   value="{{ old('name', $course->name) }}"
                   class="w-full mt-1 px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-700
                          bg-white dark:bg-azwara-darkest text-gray-800 dark:text-gray-100
                          focus:ring-2 focus:ring-primary/40 focus:outline-none"
                   required>
        </div>
        {{-- SLUG --}}
        <div>
            <label class="block font-semibold text-sm text-gray-700 dark:text-gray-200">
                Slug <span class="text-red-500">*</span>
            </label>

            <input type="text"
                name="slug"
                value="{{ old('slug', $course->slug) }}"
                class="w-full mt-1 px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-700
                        bg-white dark:bg-azwara-darkest text-gray-800 dark:text-gray-100
                        focus:ring-2 focus:ring-primary/40 focus:outline-none"
                required>

            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                Slug adalah URL unik untuk course ini. Contoh: <span class="italic">matematika-dasar</span>
            </p>
        </div>
        {{-- DESCRIPTION --}}
        <div>
            <label class="block font-semibold text-sm text-gray-700 dark:text-gray-200">
                Deskripsi <span class="text-red-500">*</span>
            </label>

            <textarea name="description"
                      rows="5"
                      class="w-full mt-1 px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-700
                             bg-white dark:bg-azwara-darkest text-gray-800 dark:text-gray-100
                             focus:ring-2 focus:ring-primary/40 focus:outline-none"
                      required>{{ old('description', $course->description) }}</textarea>
        </div>

        {{-- CURRENT THUMBNAIL --}}
        <div>
            <label class="block font-semibold text-sm text-gray-700 dark:text-gray-200">
                Thumbnail Sekarang
            </label>

            <img src="{{ $course->thumbnail ? asset('storage/'.$course->thumbnail) : asset('img/course-default.png') }}"
                 class="w-full max-w-sm h-48 rounded-xl object-cover mt-3 shadow-md">
        </div>

        {{-- NEW THUMBNAIL --}}
        <div class="p-4 border border-gray-300 dark:border-gray-700 rounded-xl bg-white dark:bg-azwara-darkest/50">

            <label class="block font-semibold text-sm text-gray-700 dark:text-gray-200">
                Ganti Thumbnail
            </label>

            <input type="file"
                name="thumbnail"
                class="mt-3 text-sm text-gray-700 dark:text-gray-200
                        file:bg-primary file:text-white file:px-4 file:py-2 file:rounded-lg
                        file:border-none file:mr-4">

            <p class="text-xs text-gray-500 dark:text-gray-300 mt-2">
                Kosongkan jika tidak ingin mengganti thumbnail.
            </p>

        </div>

        {{-- UPDATE BUTTON --}}
        <div class="pt-4">
            <button class="px-5 py-2.5 rounded-xl bg-primary text-white
                           hover:bg-azwara-medium transition shadow-md">
                Update Course
            </button>
        </div>

    </form>

</div>

@endsection
@push('scripts')
<script>
document.querySelector('input[name="name"]').addEventListener('input', function() {
    let slugField = document.querySelector('input[name="slug"]');
    slugField.value = this.value
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/(^-|-$)/g, '');
});
</script>
@endpush
