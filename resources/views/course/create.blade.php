@extends('layouts.app')

@section('content')
{{-- Tombol Kembali --}}
<a href="{{ route('tentor.index') }}"
    class="inline-flex items-center gap-2
            text-sm font-medium
            text-azwara-darkest dark:text-azwara-lighter
            hover:text-primary
            transition">

    {{-- Panah kiri --}}
    <svg xmlns="http://www.w3.org/2000/svg"
            class="w-4 h-4"
            fill="none" viewBox="0 0 24 24"
            stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round"
                stroke-linejoin="round"
                d="M15 19l-7-7 7-7" />
    </svg>

    Kembali
</a>
<div class="max-w-xl mx-auto px-4 py-8">

    <h1 class="text-2xl font-bold mb-6 text-gray-800 dark:text-gray-100">
        Tambah Course
    </h1>

    {{-- GLOBAL VALIDATION ERROR --}}
    @if ($errors->any())
        <div class="p-4 mb-4 rounded-lg bg-red-100 text-red-700 border border-red-300">
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    <form action="{{ route('course.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf

        {{-- Nama Course --}}
        <div class="space-y-1">
            <label class="font-semibold text-gray-800 dark:text-gray-100">
                Nama Course <span class="text-red-500">*</span>
            </label>

            <input type="text" name="name" value="{{ old('name') }}" required
                class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700
                       bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100
                       focus:ring-2 focus:ring-primary focus:border-transparent transition">

            @error('name')
                <p class="text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        {{-- Slug --}}
        <div class="space-y-1">
            <label class="font-semibold text-gray-800 dark:text-gray-100">
                Slug <span class="text-red-500">*</span>
            </label>

            <input type="text" name="slug" value="{{ old('slug') }}" required
                class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700
                       bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100
                       focus:ring-2 focus:ring-primary focus:border-transparent transition">

            @error('slug')
                <p class="text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        {{-- Deskripsi --}}
        <div class="space-y-1">
            <label class="font-semibold text-gray-800 dark:text-gray-100">
                Deskripsi <span class="text-red-500">*</span>
            </label>

            <textarea name="description" rows="4" required
                class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700
                       bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100
                       focus:ring-2 focus:ring-primary focus:border-transparent transition">{{ old('description') }}</textarea>

            @error('description')
                <p class="text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        {{-- Thumbnail --}}
        <div class="space-y-1">
            <label class="font-semibold text-gray-800 dark:text-gray-100">
                Thumbnail <span class="text-red-500">*</span>
            </label>

            <input type="file" name="thumbnail" required
                class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700
                       bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100
                       file:mr-4 file:py-2 file:px-4
                       file:rounded-lg file:border-0
                       file:bg-primary file:text-white
                       hover:file:bg-primary/90 transition">

            @error('thumbnail')
                <p class="text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <button
            class="px-4 py-2 bg-primary text-white rounded-lg shadow hover:bg-primary/90 transition w-full">
            Simpan
        </button>

    </form>

</div>

@endsection
