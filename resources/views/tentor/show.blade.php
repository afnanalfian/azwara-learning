@extends('layouts.app')

@section('content')
<div class="min-h-screen">

    <div class="max-w-2xl mx-auto">

        {{-- Back --}}
        <a href="{{ route('tentor.index') }}"
           class="inline-flex items-center gap-2 text-sm font-medium mb-4
                  text-gray-700 dark:text-gray-300 hover:text-primary transition">

            <svg xmlns="http://www.w3.org/2000/svg"
                 class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15 19l-7-7 7-7" />
            </svg>
            Kembali
        </a>

        {{-- CARD --}}
        <div class="rounded-2xl shadow-lg border border-gray-200/70 dark:border-gray-700/50
                    bg-white dark:bg-azwara-darker p-5 sm:p-6">

        {{-- HEADER --}}
        <div class="flex flex-col sm:flex-row sm:items-center gap-4 mb-5">

            {{-- Avatar --}}
            <img src="{{ $teacher->user->avatar_url }}"
                class="w-12 h-12 sm:w-20 sm:h-20 rounded-full object-cover shadow shrink-0">

            <div class="flex-1 min-w-0">
                <h1 class="text-base sm:text-xl font-semibold text-gray-900 dark:text-gray-100 break-words">
                    {{ $teacher->user->name }}
                </h1>

                @role('admin')
                <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-300">
                    {{ $teacher->user->email }}
                </p>

                <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-300">
                    {{ $teacher->user->phone ?? '-' }}
                </p>
                @endrole
            </div>

            {{-- Status Badge --}}
            <div class="sm:ml-auto">
                @if($teacher->user->is_active)
                    <span class="px-2 py-0.5 text-xs rounded-full
                                bg-green-100 text-green-700
                                dark:bg-green-900/30 dark:text-green-300">
                        Aktif
                    </span>
                @else
                    <span class="px-2 py-0.5 text-xs rounded-full
                                bg-red-100 text-red-700
                                dark:bg-red-900/30 dark:text-red-300">
                        Nonaktif
                    </span>
                @endif
            </div>
        </div>

        <div class="border-t border-gray-200 dark:border-gray-700 my-4"></div>

        {{-- LOCATION --}}
        <h2 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-2">
            Informasi Domisili
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm mb-6">
            <div class="p-3 rounded-lg bg-gray-100 dark:bg-azwara-darkest/40">
                <p class="text-gray-500 text-xs">Provinsi</p>
                <p class="font-semibold text-gray-900 dark:text-gray-200">
                    {{ $teacher->user->province->name ?? '-' }}
                </p>
            </div>

            <div class="p-3 rounded-lg bg-gray-100 dark:bg-azwara-darkest/40">
                <p class="text-gray-500 text-xs">Kab / Kota</p>
                <p class="font-semibold text-gray-900 dark:text-gray-200">
                    {{ $teacher->user->regency->name ?? '-' }}
                </p>
            </div>
        </div>

        {{-- BIO --}}
        <h2 class="text-sm font-semibold text-gray-800 dark:text-gray-200">
            Bio
        </h2>
        <p class="mt-1 text-sm text-gray-700 dark:text-gray-300 leading-relaxed break-words">
            {!! nl2br(e($teacher->bio ?? '-')) !!}
        </p>

        {{-- COURSES --}}
        <div class="mt-6">
            <h2 class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                Course Yang Diajarkan
            </h2>

            <div class="flex flex-wrap gap-2 mt-2">
                @forelse($teacher->courses as $c)
                    <span class="px-3 py-1 rounded-lg text-xs font-medium
                                bg-primary/10 text-primary dark:bg-primary/20 dark:text-primary/90">
                        {{ $c->name }}
                    </span>
                @empty
                    <p class="text-gray-600 dark:text-gray-300 text-sm">Tidak ada.</p>
                @endforelse
            </div>
        </div>

        @role('admin')
        {{-- ACTIONS --}}
        <div class="mt-8 grid grid-cols-2 sm:flex sm:flex-row gap-3">

            {{-- WA --}}
            <a href="https://wa.me/{{ $teacher->user->phone }}" target="_blank"
            class="flex items-center justify-center px-3 py-2 rounded-lg
                    bg-green-600 hover:bg-green-700 text-white text-sm w-full text-center">
                WhatsApp
            </a>

            {{-- EDIT --}}
            <a href="{{ route('tentor.edit', $teacher->id) }}"
            class="flex items-center justify-center px-3 py-2 rounded-lg
                    bg-primary hover:bg-primary/90 text-white text-sm w-full text-center">
                Edit
            </a>

            {{-- TOGGLE ACTIVE --}}
            <form method="POST"
                action="{{ route('tentor.toggle', $teacher->id) }}"
                class="w-full sweet-confirm"
                data-message="Yakin ingin mengubah status tentor ini?">
                @csrf
                <button class="w-full px-3 py-2 rounded-lg text-white text-sm
                    {{ $teacher->user->is_active ? 'bg-red-600 hover:bg-red-700' : 'bg-blue-600 hover:bg-blue-700' }}">
                    {{ $teacher->user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                </button>
            </form>

            {{-- REMOVE --}}
            <form method="POST"
                action="{{ route('tentor.remove', $teacher->id) }}"
                class="w-full sweet-confirm"
                data-message="Yakin ingin menghapus tentor ini? Data tidak akan bisa dikembalikan.">
                @csrf
                @method('DELETE')
                <button class="w-full px-3 py-2 rounded-lg bg-gray-600 hover:bg-gray-700
                            text-white text-sm">
                    Hapus
                </button>
            </form>

        </div>
        @endrole
        </div>
    </div>
</div>
@endsection
