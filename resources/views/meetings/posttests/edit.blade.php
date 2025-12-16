@extends('layouts.app')

@section('content')
<div x-data="{ openAddQuestion: false }" class="max-w-6xl mx-auto space-y-8">

    {{-- ================= HEADER ================= --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-azwara-darker dark:text-azwara-lighter">
                Edit Post Test
            </h1>

            <p class="text-gray-600 dark:text-gray-300">
                {{ $postTest->meeting->title }}
            </p>
        </div>

        @php
            $statusColor = match($postTest->status) {
                'inactive' => 'bg-gray-400',
                'active'   => 'bg-green-600',
                'closed'   => 'bg-red-600',
            };
        @endphp

        <span
            class="inline-flex w-fit items-center
                px-2 py-0.5 md:px-3 md:py-1
                text-xs md:text-sm
                rounded-full text-white {{ $statusColor }}">
            {{ ucfirst($postTest->status) }}
        </span>
    </div>

    {{-- ================= DURASI ================= --}}
    <div class="bg-azwara-lightest dark:bg-secondary/80
                rounded-2xl p-6
                border border-azwara-light/30 dark:border-white/10">

        <h2 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-100">
            Durasi Post Test (menit)
        </h2>

        <form method="POST"
              action="{{ route('posttest.duration.update', $postTest) }}"
              class="flex flex-col sm:flex-row gap-4 items-start sm:items-end">

            @csrf

            <div class="w-full sm:w-64">

                <input type="number"
                       name="duration_minutes"
                       min="1"
                       value="{{ old('duration_minutes', $postTest->duration_minutes) }}"
                       class="w-full rounded-lg
                              border-gray-300
                              dark:border-white/10
                              bg-white dark:bg-secondary
                              dark:text-white
                              focus:ring-primary focus:border-primary"
                       @disabled($postTest->status !== 'inactive')>
            </div>

            @if($postTest->status === 'inactive')
                <button type="submit"
                        class="px-5 py-2 rounded-lg
                               bg-primary text-white hover:bg-primary/90 transition">
                    Simpan Durasi
                </button>
            @endif
        </form>
    </div>

    @include('meetings.posttests.partials.selected-questions')

    {{-- ================= ACTION ================= --}}
    <div class="flex flex-wrap gap-3">

        @if($postTest->status === 'inactive')
            <form method="POST"
                  action="{{ route('posttest.launch', $postTest) }}"
                  class="sweet-confirm"
                  data-message="Yakin ingin memulai post test?">
                @csrf
                <button class="px-5 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700">
                    Launch Post Test
                </button>
            </form>
        @endif

        @if($postTest->status === 'active')
            <form method="POST"
                  action="{{ route('posttest.close', $postTest) }}"
                  class="sweet-confirm"
                  data-message="Yakin ingin menutup post test?">
                @csrf
                <button class="px-5 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700">
                    Tutup Post Test
                </button>
            </form>
        @endif

        <a href="{{ route('meeting.show', $postTest->meeting) }}"
           class="px-4 py-2 rounded-lg
                  text-gray-600 dark:text-gray-300
                  hover:bg-gray-100 dark:hover:bg-white/5">
            Kembali ke Meeting
        </a>
    </div>

@include('meetings.posttests.partials.add-question-modal')
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
