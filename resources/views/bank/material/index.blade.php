@extends('layouts.app')

@section('content')

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">

    {{-- TITLE --}}
    <div>
        <h1 class="text-2xl font-bold text-azwara-darker dark:text-azwara-lighter">
            Materi: {{ $category->name }}
        </h1>
        <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
            Daftar materi dalam kategori ini
        </p>
    </div>

    {{-- SEARCH + ADD --}}
    <div class="flex flex-col sm:flex-row sm:items-center gap-3">

        {{-- SEARCH --}}
        <form method="GET" action="{{ route('bank.category.materials.index', $category->id) }}" class="flex w-full sm:w-auto gap-2">
            <input type="text"
                name="q"
                placeholder="Cari materi..."
                value="{{ request('q') }}"
                class="flex-1 px-3 py-2 rounded-xl border border-gray-300 dark:border-gray-700
                       bg-white dark:bg-azwara-darkest text-gray-800 dark:text-gray-100
                       focus:ring-2 focus:ring-primary/40 focus:outline-none w-full sm:w-64">

            <button type="submit"
                class="px-4 py-2 rounded-xl bg-azwara-medium text-white hover:opacity-95 transition">
                Cari
            </button>
        </form>

        {{-- ADD BUTTON --}}
        @role('admin')
        <a href="{{ route('bank.category.materials.create', $category->id) }}"
           class="px-4 py-2 bg-azwara-darker text-white rounded-xl hover:bg-azwara-medium transition">
            + Tambah Materi
        </a>
        @endrole

    </div>
</div>

{{-- Tombol Kembali --}}
<div class="mb-4">
    <a href="{{ route('bank.category.index') }}"
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
{{-- LIST TABLE STYLE --}}
<div class="space-y-4">

    @forelse ($materials as $m)
        <div class="w-full bg-white dark:bg-azwara-darker border border-gray-200 dark:border-azwara-darkest
                    rounded-xl p-5 sm:p-6 shadow-sm hover:shadow-lg transition">

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

                {{-- LEFT: TITLE --}}
                <a href="{{ route('bank.material.questions.index', $m->id) }}"
                class="flex-1 block">
                    <h3 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 truncate">
                        {{ $m->name }}
                    </h3>
                </a>

                {{-- RIGHT: NUMBER + ACTIONS --}}
                <div class="flex items-center gap-4 sm:gap-6">

                    {{-- NUMBER OF QUESTIONS --}}
                    <div class="text-lg text-gray-700 dark:text-gray-300 font-medium whitespace-nowrap">
                        <span class="text-gray-900 dark:text-gray-100 font-bold">
                            {{ $m->questions->count() }}
                        </span>
                        Soal
                    </div>

                    {{-- ACTION BUTTONS --}}
                    @role('admin')
                    <div class="flex items-center gap-2">

                        <a href="{{ route('bank.material.edit', $m->id) }}"
                            class="px-3 py-1.5 rounded-lg text-[15px] font-medium
                                bg-azwara-medium/10 text-azwara-darker dark:text-azwara-lighter
                                hover:bg-azwara-medium/20 transition">
                            Edit
                        </a>

                        <form method="POST"
                            action="{{ route('bank.material.delete', $m->id) }}"
                            class="sweet-confirm"
                            data-message="Yakin ingin menghapus materi ini? Semua soal di dalamnya akan ikut terhapus secara permanen.">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="px-3 py-1.5 rounded-lg text-[15px] font-medium
                                    bg-red-600 text-white hover:bg-red-700 transition">
                                Hapus
                            </button>
                        </form>

                    </div>
                    @endrole

                </div>

            </div>

        </div>
    @empty

        <div class="w-full text-center py-12 bg-white dark:bg-azwara-darker
                    rounded-xl border border-gray-200 dark:border-azwara-darkest">
            <p class="text-gray-600 dark:text-gray-300">Belum ada materi dalam kategori ini.</p>
        </div>

    @endforelse

</div>




{{-- PAGINATION --}}
<div class="mt-6">
    {{ $materials->links() }}
</div>

@endsection
