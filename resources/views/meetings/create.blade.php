@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">

    {{-- HEADER --}}
    <div>
        <h1 class="text-2xl font-bold text-azwara-darker dark:text-azwara-lighter">
            Tambah Pertemuan
        </h1>
        <p class="mt-1 text-gray-600 dark:text-gray-300">
            Buat jadwal pertemuan baru untuk course
            <span class="font-medium">{{ $course->title }}</span>
        </p>
    </div>

    {{-- FORM --}}
    <form action="{{ route('meeting.store', $course) }}"
          method="POST"
          class="space-y-6
                 bg-white dark:bg-secondary/80
                 p-6 rounded-2xl
                 border border-azwara-light/30 dark:border-white/10">

        @csrf

        {{-- TITLE --}}
        <div>
            <label class="block text-sm font-medium mb-1
                          text-gray-700 dark:text-gray-200">
                Judul Pertemuan
            </label>

            <input type="text"
                   name="title"
                   value="{{ old('title') }}"
                   required
                   class="w-full rounded-lg
                          border-gray-300
                          dark:border-white/10
                          bg-white dark:bg-secondary
                          dark:text-white
                          focus:ring-primary focus:border-primary">
        </div>

        {{-- DATETIME --}}
        <div>
            <label class="block text-sm font-medium mb-1
                          text-gray-700 dark:text-gray-200">
                Tanggal & Jam Mulai
            </label>

            <input type="datetime-local"
                name="scheduled_at"
                value="{{ old('scheduled_at') }}"
                class="w-full rounded-lg
                        border-gray-300
                        dark:border-white/10
                        bg-white dark:bg-secondary
                        dark:text-white
                        focus:ring-primary focus:border-primary">

            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                Waktu menggunakan zona WIB (UTC+7)
            </p>
        </div>

        {{-- ZOOM LINK --}}
        <div>
            <label class="block text-sm font-medium mb-1
                          text-gray-700 dark:text-gray-200">
                Link Zoom (opsional)
            </label>

            <input type="url"
                   name="zoom_link"
                   value="{{ old('zoom_link') }}"
                   placeholder="https://zoom.us/..."
                   class="w-full rounded-lg
                          border-gray-300
                          dark:border-white/10
                          bg-white dark:bg-secondary
                          dark:text-white
                          focus:ring-primary focus:border-primary">
        </div>

        {{-- ACTION --}}
        <div class="flex items-center justify-end gap-3 pt-4 border-t
                    border-gray-200 dark:border-white/10">

            <a href="{{ route('course.show', $course->slug) }}"
               class="px-4 py-2 rounded-lg
                      text-gray-600 dark:text-gray-300
                      hover:bg-gray-100 dark:hover:bg-white/5">
                Batal
            </a>

            <button type="submit"
                    class="px-5 py-2 rounded-lg
                           bg-primary text-white
                           hover:bg-primary/90
                           transition">
                Simpan Pertemuan
            </button>
        </div>
    </form>
</div>
@endsection
