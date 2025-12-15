@extends('layouts.app')

@section('content')

<div class="max-w-xl mx-auto px-4 py-8">

    <h1 class="text-2xl font-bold mb-6 text-gray-800 dark:text-gray-100">
        Tambah Materi untuk: {{ $category->name }}
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

    <form
        action="{{ route('bank.category.materials.store', $category) }}"
        method="POST"
        class="space-y-5">

        @csrf

        {{-- Name --}}
        <div class="space-y-1">
            <label class="font-semibold text-gray-800 dark:text-gray-100">
                Nama Materi <span class="text-red-500">*</span>
            </label>

            <input type="text" name="name" value="{{ old('name') }}" required
                class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700
                       bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100
                       focus:ring-2 focus:ring-primary focus:border-transparent transition">

            @error('name')
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
