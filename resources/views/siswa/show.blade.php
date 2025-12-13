@extends('layouts.app')

@section('content')
<div class="min-h-screen">

    <div class="max-w-3xl mx-auto">
        {{-- Tombol Kembali --}}
        <div class="mb-4">
            <a href="{{ route('siswa.index') }}"
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
        </div>

        {{-- Card --}}
        <div
            class="bg-white dark:bg-azwara-darker
                   border border-gray-200 dark:border-azwara-darkest
                   rounded-3xl shadow-xl dark:shadow-black/30
                   p-6 sm:p-8">

            {{-- Header --}}
            <div class="flex items-center gap-5">
                <img
                    src="{{ $user->avatar_url }}"
                    class="w-14 h-14 sm:w-20 sm:h-20 rounded-full object-cover flex-shrink-0"
                />

                <div class="min-w-0">
                    <h2
                        class="text-base sm:text-xl font-bold
                            text-azwara-darkest dark:text-azwara-lighter
                            truncate max-w-[180px] sm:max-w-none">
                        {{ $user->name }}
                    </h2>

                    <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-300 truncate">
                        {{ $user->email }}
                    </p>

                    <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">
                        {{ $user->phone }}
                    </p>
                </div>

                {{-- Status --}}
                <div class="ml-auto">
                    @if($user->is_active)
                        <span class="px-3 py-1 rounded-full text-xs font-medium
                                     bg-green-100 text-green-700
                                     dark:bg-green-900/30 dark:text-green-400">
                            Aktif
                        </span>
                    @else
                        <span class="px-3 py-1 rounded-full text-xs font-medium
                                     bg-red-100 text-red-600
                                     dark:bg-red-900/30 dark:text-red-400">
                            Nonaktif
                        </span>
                    @endif
                </div>
            </div>

            {{-- Divider --}}
            <div class="my-6 border-t border-gray-200 dark:border-gray-700"></div>

            {{-- Info --}}
            <div class="grid sm:grid-cols-2 gap-4 text-sm">
                <div
                    class="rounded-xl bg-gray-50 dark:bg-azwara-darkest/50
                           px-4 py-3">
                    <p class="text-gray-500 dark:text-gray-400">Provinsi</p>
                    <p class="font-semibold text-gray-900 dark:text-gray-100">
                        {{ $user->province->name ?? '-' }}
                    </p>
                </div>

                <div
                    class="rounded-xl bg-gray-50 dark:bg-azwara-darkest/50
                           px-4 py-3">
                    <p class="text-gray-500 dark:text-gray-400">Kab / Kota</p>
                    <p class="font-semibold text-gray-900 dark:text-gray-100">
                        {{ $user->regency->name ?? '-' }}
                    </p>
                </div>
            </div>

            {{-- Actions --}}
            <div class="mt-8 flex flex-col sm:flex-row gap-3">

                {{-- WhatsApp --}}
                <a
                    href="https://wa.me/{{ $user->phone }}"
                    target="_blank"
                    class="inline-flex items-center justify-center gap-2
                           px-4 py-3 rounded-xl
                           bg-green-600 text-white font-medium
                           hover:opacity-90 transition"
                >
                    {{-- icon --}}
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5"
                         fill="none" viewBox="0 0 24 24"
                         stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 21l1.65-3.3A8.99 8.99 0 1121 12a9 9 0 01-9 9H3z"/>
                    </svg>
                    WhatsApp
                </a>

                {{-- Toggle Active --}}
                <form method="POST"
                    action="{{ route('siswa.toggle', $user->id) }}"
                    class="sweet-confirm"
                    data-message="{{ $user->is_active
                        ? 'Yakin ingin menonaktifkan akun siswa ini?'
                        : 'Yakin ingin mengaktifkan kembali akun siswa ini?' }}">

                    @csrf

                    <button
                        class="w-full inline-flex items-center justify-center gap-2
                            px-4 py-3 rounded-xl font-medium transition
                            {{ $user->is_active
                                    ? 'bg-red-600 text-white hover:bg-red-700'
                                    : 'bg-primary text-white hover:opacity-90' }}">
                        {{ $user->is_active ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}
                    </button>
                </form>

            </div>

        </div>
    </div>
</div>
@endsection
