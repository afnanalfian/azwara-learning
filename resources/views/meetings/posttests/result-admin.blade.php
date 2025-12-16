@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-10">

<a href="{{ route('meeting.show', $postTest->meeting) }}"
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

    {{-- ================= HEADER ================= --}}
    <div>
        <h1 class="text-2xl font-bold text-azwara-darker dark:text-azwara-lighter">
            Hasil Post Test
        </h1>

        <p class="text-gray-600 dark:text-gray-300">
            {{ $postTest->meeting->title }}
        </p>
    </div>

    {{-- ================= RANKING ================= --}}
    <div class="bg-azwara-lightest dark:bg-secondary/80
                rounded-2xl p-6 dark:text-azwara-lighter
                border border-azwara-light/30 dark:border-white/10">

        <h2 class="text-lg font-semibold mb-4">
            🏆 Peringkat Siswa
        </h2>

        <table class="w-full text-md">
            <thead class="hidden md:table-header-group
                        border-b border-white/10">
                <tr class="text-left">
                    <th class="py-2 pr-4">Rank</th>
                    <th class="pr-6">Nama</th>
                    <th class="pr-6 text-left">Nilai</th>
                    <th class="text-left">Durasi</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                @forelse($attempts as $i => $attempt)
                    <tr class="block md:table-row py-4 md:py-0">

                        {{-- RANK --}}
                        <td class="hidden md:table-cell py-2 font-semibold">
                            {{ $i + 1 }}
                        </td>

                        {{-- NAMA + INFO MOBILE --}}
                        <td class="block md:table-cell space-y-2">

                            {{-- Nama --}}
                            <div class="font-medium text-gray-900 dark:text-white">
                                {{ $attempt->user->name }}
                            </div>

                            {{-- INFO MOBILE --}}
                            <div class="flex flex-wrap gap-x-4 gap-y-1
                                        text-xs text-gray-600 dark:text-gray-400
                                        md:hidden">

                                <span>Rank: <strong>{{ $i + 1 }}</strong></span>

                                <span>Nilai:
                                    <strong class="text-primary">
                                        {{ $attempt->score }}
                                    </strong>
                                </span>

                                <span>
                                    {{ floor(abs($attempt->duration_seconds / 60)) }} m
                                    {{ abs($attempt->duration_seconds % 60) }} d
                                </span>
                            </div>
                        </td>

                        {{-- NILAI DESKTOP --}}
                        <td class="hidden md:table-cell pr-6 font-semibold text-primary">
                            {{ $attempt->score }}
                        </td>

                        {{-- DURASI DESKTOP --}}
                        <td class="hidden md:table-cell text-gray-200">
                            {{ floor(abs($attempt->duration_seconds / 60)) }} menit
                            {{ abs($attempt->duration_seconds % 60) }} detik
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4"
                            class="py-6 text-center text-gray-500">
                            Belum ada siswa mengerjakan
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ================= SOAL & STATISTIK ================= --}}
    <div class="space-y-6">

        <h2 class="text-lg font-semibold dark:text-azwara-lightest">
            📊 Analisis Soal
        </h2>

        @foreach($postTest->questions as $i => $pq)
            @php
                $question = $pq->question;
                $stat = $questionStats[$question->id] ?? ['correct'=>0,'total'=>0];
            @endphp

            <div class="p-6 rounded-2xl dark:text-azwara-lightest
                        bg-azwara-lightest dark:bg-secondary/80
                        border border-gray-200 dark:border-white/10 space-y-4">

                {{-- HEADER --}}
                <div class="flex items-start justify-between gap-4">
                    <h3 class="font-semibold">
                        Soal {{ $i + 1 }}
                    </h3>

                    <span class="px-3 py-1 rounded-full tx-md
                                 bg-blue-600 text-white">
                        {{ $stat['correct'] }}/{{ $stat['total'] }} benar
                    </span>
                </div>

                {{-- QUESTION --}}
                <div class="space-y-3">
                    <div class="prose dark:prose-invert max-w-none">
                        {!! $question->question_text !!}
                    </div>

                    {{-- GAMBAR SOAL --}}
                    @if ($question->image)
                        <img
                            src="{{ Storage::url($question->image) }}"
                            alt="Gambar soal"
                            class="mx-auto max-h-48 md:max-h-64 rounded-lg border
                                bg-white p-2 object-contain">
                    @endif
                </div>

                {{-- OPTIONS --}}
                <div class="space-y-2">
                    @foreach($question->options as $option)
                        <div
                            class="p-3 rounded-lg border
                                {{ $option->is_correct
                                    ? 'border-green-500 bg-green-50 dark:bg-green-500/10'
                                    : 'border-gray-200 dark:border-white/10' }}">

                            {{-- LABEL + TEKS --}}
                            <div class="flex items-start gap-2">

                                @if ($question->type !== 'truefalse')
                                    <span class="font-semibold shrink-0">
                                        {{ $option->label }}.
                                    </span>
                                @endif

                                <div class="prose dark:prose-invert max-w-none tx-md">
                                    {!! $option->option_text !!}
                                </div>
                            </div>

                            {{-- GAMBAR OPSI --}}
                            @if ($option->image)
                                <img
                                    src="{{ Storage::url($option->image) }}"
                                    alt="Gambar opsi"
                                    class="mt-2 max-h-32 md:max-h-48 rounded-lg border
                                        bg-white p-2 object-contain">
                            @endif
                        </div>
                    @endforeach
                </div>

                {{-- EXPLANATION (TOGGLE) --}}
                @if($question->explanation)
                    <div
                        x-data="{ open: false }"
                        class="mt-4 rounded-lg
                            bg-azwara-lighter dark:bg-secondary
                            border border-azwara-light/30 dark:border-white/10">

                        {{-- HEADER --}}
                        <button
                            type="button"
                            @click="open = !open"
                            class="w-full flex items-center justify-between
                                px-4 py-3 font-semibold
                                text-left
                                hover:bg-azwara-light/30 dark:hover:bg-white/5
                                transition">

                            <span>📘 Pembahasan</span>

                            <svg
                                :class="{ 'rotate-180': open }"
                                class="w-4 h-4 transform transition-transform"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        {{-- CONTENT --}}
                        <div
                            x-show="open"
                            x-collapse
                            class="px-4 pb-4">

                            <div class="prose dark:prose-invert max-w-none tx-md mt-3">
                                {!! $question->explanation !!}
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        @endforeach

    </div>

</div>
@endsection
@push('scripts')
<script>
window.MathJax = {
    tex: {
        inlineMath: [['\\(', '\\)']]
    }
};
</script>

<script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    if (window.MathJax) {
        MathJax.typesetPromise();
    }
});
</script>
@endpush
