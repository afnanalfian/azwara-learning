@extends('layouts.app')

@section('content')
{{-- HEADER --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">

    {{-- Title --}}
    <div>
        <h1 class="text-2xl font-bold text-azwara-darker dark:text-azwara-lighter">
            Daftar Course
        </h1>
        <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
            Kelas yang tersedia saat ini
        </p>
    </div>

    {{-- Search + Add Button --}}
    <div class="flex flex-col sm:flex-row sm:items-center gap-3">

        {{-- SEARCH --}}
        <form method="GET" action="{{ route('course.index') }}" class="flex w-full sm:w-auto gap-2">
            <input type="text"
                name="q"
                placeholder="Cari course..."
                value="{{ $q ?? '' }}"
                class="flex-1 px-3 py-2 rounded-xl border border-gray-300 dark:border-gray-700
                    bg-white dark:bg-azwara-darkest text-gray-800 dark:text-gray-100
                    focus:ring-2 focus:ring-primary/40 focus:outline-none w-full sm:w-64" />

            <button type="submit"
                class="px-4 py-2 rounded-xl bg-azwara-medium text-white hover:opacity-95 transition">
                Cari
            </button>
        </form>

        {{-- ADD BUTTON (ADMIN ONLY) --}}
        @role('admin')
        <a href="{{ route('course.create') }}"
            class="px-4 py-2  bg-azwara-darker text-white rounded-xl hover:bg-azwara-medium transition">
            + Tambah Course
        </a>
        @endrole

    </div>
</div>

{{-- GRID --}}
<div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
    @forelse($courses as $c)
        <article
            class="group bg-white dark:bg-azwara-darker rounded-2xl shadow-md overflow-hidden
                    border border-gray-100 dark:border-azwara-darkest hover:shadow-xl transition">

            <a href="{{ route('course.show', $c->slug) }}" class="block">

                {{-- IMAGE --}}
                <div class="relative">
                    <img src="{{ $c->thumbnail ? asset('storage/'.$c->thumbnail) : asset('img/course-default.png') }}"
                            alt="{{ $c->name }}"
                            class="w-full h-40 sm:h-44 object-cover">

                    {{-- overlay --}}
                    <div class="absolute inset-0 bg-gradient-to-t from-black/35 to-transparent opacity-0 group-hover:opacity-100 transition"></div>

                    {{-- badge --}}
                    <span class="absolute left-3 top-3 text-xs font-medium
                                    bg-primary/90 text-white px-3 py-1 rounded-lg shadow">
                        Course
                    </span>
                </div>

                {{-- BODY --}}
                <div class="p-4 sm:p-5">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 truncate">
                        {{ $c->name }}
                    </h3>

                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
                        {{ \Illuminate\Support\Str::limit($c->description ?? '-', 120) }}
                    </p>

                    <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                        <strong class="text-gray-700 dark:text-gray-200">Tentor:</strong>
                        {{
                            $c->teachers->isNotEmpty()
                                ? $c->teachers->map(fn($t) => $t->user->name ?? '-')->join(', ')
                                : '-'
                        }}
                    </p>
                </div>
            </a>

            {{-- FOOTER ACTIONS --}}
            @role('admin')
            <div class="flex items-center justify-between gap-3 p-3 sm:p-4 border-t border-gray-100 dark:border-azwara-darkest">

                <a href="{{ route('course.edit', $c->slug) }}"
                    class="px-3 py-1.5 rounded-lg text-sm bg-azwara-medium/10 text-azwara-darker dark:text-azwara-lighter
                        hover:bg-azwara-medium/20 transition">
                    Edit
                </a>

                {{-- DELETE WITH SWEETALERT --}}
                <form method="POST"
                    action="{{ route('course.destroy', $c->slug) }}"
                    class="sweet-confirm"
                    data-message="Yakin ingin menghapus course ini? Semua meeting dan datanya akan hilang secara permanen.">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="px-3 py-1.5 rounded-lg text-sm bg-red-600 text-white hover:bg-red-700 transition">
                        Hapus
                    </button>
                </form>

            </div>
            @endrole
        </article>
    @empty
        <div class="col-span-full text-center py-12">
            <p class="text-gray-600 dark:text-gray-300">Belum ada course. Klik “Tambah Course” untuk menambahkan baru.</p>
        </div>
    @endforelse
</div>

{{-- PAGINATION --}}
<div class="mt-6">
    {{ $courses->links() }}
</div>

@endsection
