@extends('layouts.app')

@section('content')

{{-- HEADER --}}
<div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <h1 class="text-2xl font-bold text-azwara-darker dark:text-azwara-lighter">
        Daftar Tentor
    </h1>

    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
        <form method="GET" action="{{ route('tentor.index') }}" class="flex gap-2 w-full sm:w-auto">
            <input type="text" name="q" value="{{ $q ?? '' }}"
                   placeholder="Cari nama / course"
                   class="w-full sm:w-80 px-4 py-2 rounded-xl border border-gray-300 dark:border-gray-700
                          bg-white dark:bg-azwara-darkest
                          text-gray-700 dark:text-gray-200
                          focus:ring-2 focus:ring-primary focus:outline-none" />

            <button
                class="px-4 py-2 bg-primary text-white rounded-xl hover:opacity-90 transition">
                Cari
            </button>
        </form>
        @role('admin')
            <a href="{{ route('tentor.create') }}"
            class="px-4 py-2 bg-azwara-darker text-white rounded-xl hover:bg-azwara-medium transition">
                Tambah Tentor
            </a>
        @endrole
    </div>
</div>


{{-- GRID --}}
<div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">

    @foreach ($tentor as $t)
        <a href="{{ route('tentor.show', $t->id) }}"
           class="group block rounded-2xl bg-white dark:bg-azwara-darker border border-gray-200 dark:border-gray-700
                  p-5 shadow-md hover:shadow-xl hover:-translate-y-1 transition-all duration-200">

            {{-- FOTO + NAMA --}}
            <div class="flex items-center gap-4 mb-3">
                <img src="{{ $t->user->avatar_url }}"
                     class="w-14 h-14 rounded-full object-cover ring-2 ring-primary/40 shadow-sm">

                <div class="min-w-0">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 truncate">
                        {{ $t->user->name }}
                    </h3>

                    <span class="text-xs px-2 py-0.5 rounded-full mt-1 inline-block
                        {{ $t->user->is_active
                            ? 'bg-green-100 text-green-700 dark:bg-green-800 dark:text-green-200'
                            : 'bg-red-100 text-red-700 dark:bg-red-800 dark:text-red-200' }}">
                        {{ $t->user->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>
            </div>

            {{-- BIO --}}
            <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed line-clamp-2 mb-3">
                {{ $t->bio ?: 'Tidak ada bio.' }}
            </p>

            {{-- MENGAJAR --}}
            <p class="text-xs text-gray-500 dark:text-gray-400">
                <span class="font-semibold text-gray-700 dark:text-gray-200">Mengajar:</span>
                {{ $t->courses->pluck('name')->join(', ') ?: '-' }}
            </p>

        </a>
    @endforeach

</div>


{{-- PAGINATION --}}
<div class="mt-10">
    {{ $tentor->links() }}
</div>

@endsection
