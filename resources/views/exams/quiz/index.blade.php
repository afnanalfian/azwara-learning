@extends('layouts.app')

@section('content')
<div x-data="{ open: false }" class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        {{-- Title --}}
        <h1 class="text-2xl font-bold text-azwara-darker dark:text-azwara-lighter">
            Daftar Quiz
        </h1>

        {{-- Action + Search --}}
        <form method="GET"
            action="{{ route('exams.index') }}"
            class="flex flex-col gap-2 w-full sm:flex-row sm:w-auto">

            {{-- Search Judul --}}
            <input
                type="text"
                name="q"
                value="{{ request('q') }}"
                placeholder="Cari judul quiz"
                class="w-full sm:w-64
                    rounded-xl
                    border-gray-300 dark:border-gray-700
                    bg-azwara-lightest dark:bg-azwara-darker
                    text-md text-azwara-darkest dark:text-azwara-lighter
                    focus:ring-primary focus:border-primary"
            >

            {{-- Filter Tanggal --}}
            <input
                type="date"
                name="date"
                value="{{ request('date') }}"
                class="w-full sm:w-44
                    rounded-xl
                    border-gray-300 dark:border-gray-700
                    bg-azwara-lightest dark:bg-azwara-darker
                    text-md text-azwara-darkest dark:text-azwara-lighter
                    focus:ring-primary focus:border-primary"
            >

            {{-- Button Cari --}}
            <button
                type="submit"
                class="px-4 py-2 rounded-xl
                    bg-primary text-white text-md font-medium
                    hover:opacity-90 transition">
                Cari
            </button>

            {{-- Tambah Quiz --}}
            <button
                type="button"
                @click="open = true"
                class="px-4 py-2 rounded-xl
                    border border-primary dark:text-azwara-lighter dark:border-azwara-lighter
                    text-primary text-md font-medium
                    hover:bg-primary hover:text-white
                    transition">
                Tambah Quiz
            </button>
        </form>
    </div>

    {{-- Table Card --}}
    <div
        class="bg-azwara-lightest dark:bg-azwara-darker
               border border-gray-200 dark:border-azwara-darkest
               rounded-2xl
               shadow-lg dark:shadow-black/40
               overflow-hidden">

        <div class="overflow-x-auto">
            <table class="min-w-full text-md">

                {{-- Head --}}
                <thead
                    class="bg-primary dark:bg-azwara-darkest
                           text-white dark:text-azwara-light">
                    <tr>
                        <th class="px-6 py-4 text-left font-semibold">
                            Quiz
                        </th>
                        <th class="px-6 py-4 text-center font-semibold">
                            Status
                        </th>
                        <th class="px-6 py-4 text-left font-semibold hidden sm:table-cell">
                            Tanggal
                        </th>
                    </tr>
                </thead>

                {{-- Body --}}
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($exams as $exam)
                        <tr
                            onclick="window.location='{{ route('exams.show', $exam) }}'"
                            class="cursor-pointer
                                   hover:bg-azwara-lighter dark:hover:bg-azwara-darkest
                                   transition">

                            {{-- Quiz --}}
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <div
                                        class="font-semibold
                                               text-azwara-darkest dark:text-azwara-lighter">
                                        {{ $exam->title }}
                                    </div>

                                    {{-- tanggal versi mobile --}}
                                    <div
                                        class="text-xs text-gray-500 dark:text-gray-400 sm:hidden">
                                        {{ $exam->exam_date->format('d M Y H:i') }}
                                    </div>
                                </div>
                            </td>

                            {{-- Status --}}
                            <td class="px-6 py-4 text-center">
                                <span
                                    class="px-3 py-1 rounded-full text-xs font-medium
                                           bg-primary/10 text-primary
                                           dark:bg-primary/20 dark:text-azwara-lighter">
                                    {{ ucfirst($exam->status) }}
                                </span>
                            </td>

                            {{-- Tanggal --}}
                            <td
                                class="px-6 py-4 text-gray-600 dark:text-gray-300
                                       hidden sm:table-cell">
                                {{ $exam->exam_date->format('d M Y H:i') }}
                            </td>
                        </tr>
                    @empty
                        {{-- Empty State --}}
                        <tr>
                            <td colspan="3" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <div
                                        class="w-14 h-14 rounded-full
                                               bg-azwara-light/30
                                               dark:bg-azwara-darkest
                                               flex items-center justify-center
                                               text-azwara-medium">
                                        T
                                    </div>

                                    <p
                                        class="text-base font-semibold
                                               text-azwara-darkest dark:text-azwara-lighter">
                                        Belum Ada Quiz
                                    </p>

                                    <p
                                        class="text-md text-gray-500 dark:text-gray-400">
                                        Data quiz akan muncul setelah tersedia
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
    </div>

    {{-- Pagination --}}
    <div>
        {{ $exams->links() }}
    </div>

@include('exams.partials._create-modal', ['type' => 'quiz'])
</div>

@endsection
