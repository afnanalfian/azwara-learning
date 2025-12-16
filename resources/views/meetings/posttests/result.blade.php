@extends('layouts.app')

@section('content')
<a href="{{ route('meeting.show', $attempt->postTest->meeting) }}"
    class="inline-flex items-center gap-2
            text-sm font-medium mt-3
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
<div class="max-w-5xl mx-auto p-2 space-y-5">

    {{-- ================= HEADER ================= --}}
    <div class="text-center space-y-6">

        <h1 class="text-3xl font-bold text-azwara-darker dark:text-azwara-lightest">
            Hasil Post Test
        </h1>

        <p class="text-base text-gray-600 dark:text-azwara-lightest/80">
            {{ $attempt->postTest->meeting->title }}
        </p>

        {{-- INFO CARD --}}
        <div class="max-w-3xl mx-auto
                    bg-azwara-lightest dark:bg-secondary/80
                    border border-azwara-light/30 dark:border-white/10
                    rounded-2xl p-5">

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-center">

                {{-- NAMA --}}
                <div class="space-y-1">
                    <div class="text-sm font-semibold text-gray-500 dark:text-azwara-lightest/70">
                        Nama Siswa
                    </div>
                    <div class="text-lg font-semibold text-azwara-darker dark:text-azwara-lightest">
                        {{ $attempt->user->name }}
                    </div>
                </div>

                {{-- SKOR --}}
                <div class="space-y-1">
                    <div class="text-sm font-semibold text-gray-500 dark:text-azwara-lightest/70">
                        Skor
                    </div>
                    <div class="text-2xl font-bold text-primary">
                        {{ $attempt->score ?? 0 }}
                    </div>
                </div>

                {{-- DURASI --}}
                @php
                    $duration = abs($attempt->duration_seconds);
                @endphp
                <div class="space-y-1">
                    <div class="text-sm font-semibold text-gray-500 dark:text-azwara-lightest/70">
                        Durasi
                    </div>
                    <div class="text-lg font-semibold text-azwara-darker dark:text-azwara-lightest">
                        {{ floor($duration / 60) }} menit {{ $duration % 60 }} detik
                    </div>
                </div>

            </div>
        </div>
    </div>



    {{-- ================= RESULT DETAIL ================= --}}
    <div class="space-y-6 dark:text-azwara-lightest">

        @foreach($attempt->postTest->questions as $i => $pq)
            @php
                $question = $pq->question;
                $answer = $attempt->answers
                    ->where('question_id', $question->id)
                    ->first();

                $selected = $answer?->selected_options ?? [];

                $isCorrect = $answer?->is_correct;
            @endphp

            <div class="p-6 rounded-2xl
                        border border-gray-200 dark:border-white/10
                        bg-azwara-lightest dark:bg-secondary/80 space-y-4">

                {{-- HEADER --}}
                <div class="flex items-start justify-between gap-4">
                    <h2 class="font-semibold">
                        Soal {{ $i + 1 }}
                    </h2>

                    @if($isCorrect)
                        <span class="px-3 py-1 rounded-full text-sm
                                    bg-green-600 text-white">
                            Benar
                        </span>
                    @else
                        <span class="px-3 py-1 rounded-full text-sm
                                    bg-red-600 text-white">
                            Salah
                        </span>
                    @endif
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
                        @php
                            $isSelected = in_array($option->id, $selected);
                            $isCorrectOption = $option->is_correct;
                        @endphp

                        <div
                            class="p-3 rounded-lg border
                                @if($isCorrectOption)
                                    border-green-500 bg-green-50 dark:bg-green-500/10
                                @elseif($isSelected)
                                    border-red-500 bg-red-50 dark:bg-red-500/10
                                @else
                                    border-gray-200 dark:border-white/10
                                @endif">

                            {{-- LABEL + TEKS --}}
                            <div class="flex items-start gap-2">

                                @if ($question->type !== 'truefalse')
                                    <span class="font-semibold shrink-0">
                                        {{ $option->label }}.
                                    </span>
                                @endif

                                <div class="prose dark:prose-invert max-w-none text-sm">
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
                            bg-azwara-lightest dark:bg-secondary
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
                        <div x-show="open" x-collapse class="px-4 pb-4">
                            <div class="prose dark:prose-invert max-w-none text-sm mt-3">
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
