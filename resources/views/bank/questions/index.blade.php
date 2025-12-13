@extends('layouts.app')

@section('content')

{{-- HEADER --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-azwara-darker dark:text-azwara-lighter">
            Soal: {{ $material->name }}
        </h1>
        <p class="text-sm text-gray-600 dark:text-gray-300">Total Soal: {{ $questions->total() }}</p>
    </div>

    @role('admin|tentor')
        <a href="{{ route('bank.material.questions.create', $material->id) }}"
           class="px-4 py-2 bg-azwara-darker text-white rounded-xl hover:bg-azwara-medium transition">
            + Tambah Soal
        </a>
    @endrole
</div>
{{-- Tombol Kembali --}}
<div class="mb-4">
    <a href="{{ route('bank.category.materials.index', $material->category_id) }}"
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

{{-- QUESTION LIST --}}
<div class="space-y-6">

@forelse ($questions as $index => $q)
    <div class="bg-white dark:bg-azwara-darker border border-gray-200 dark:border-azwara-darkest
                rounded-xl p-6 shadow-sm">

        {{-- HEADER --}}
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                Soal {{ ($questions->currentPage() - 1) * $questions->perPage() + ($index + 1) }}
            </h2>

            <span class="px-3 py-1.5 text-sm rounded-lg bg-primary/10 text-primary font-semibold">
                {{ strtoupper($q->type) }}
            </span>
        </div>

        {{-- QUESTION TEXT --}}
        <div class="prose dark:prose-invert max-w-none leading-relaxed mb-4 text-gray-800 dark:text-gray-100">
            {!! $q->question_text !!}
        </div>

        {{-- OPTIONS (MCQ & MCMA) --}}
        @if (in_array($q->type, ['mcq', 'mcma']))
            <div class="space-y-3 mb-4">

                @foreach ($q->options as $opt)
                    <div class="border rounded-lg px-4 py-3
                                flex items-center justify-between
                                hover:bg-gray-50 dark:hover:bg-gray-700 transition">

                        <div class="text-base text-gray-800 dark:text-gray-100">
                            <strong>{{ strtoupper($opt->key) }}</strong>
                            {!! $opt->option_text !!}
                        </div>

                        @if ($opt->is_correct)
                            <span class="text-green-600 font-bold text-sm">✔</span>
                        @endif
                    </div>
                @endforeach

            </div>
        @endif


        {{-- TRUE / FALSE --}}
        @if ($q->type === 'truefalse')
            <div class="space-y-3 mb-4">

                @foreach ($q->options as $i => $opt)
                    <div class="border rounded-lg px-4 py-3
                                flex items-center justify-between">

                        <div class="text-base text-gray-800 dark:text-gray-100">
                            {!! $opt->option_text !!}
                        </div>

                        @if ($opt->is_correct)
                            <span class="text-green-600 font-bold text-sm">✔</span>
                        @endif

                    </div>
                @endforeach

            </div>
        @endif


        {{-- TOGGLE PEMBAHASAN --}}
        <div x-data="{ open: false }">

            <button @click="open = !open"
                class="px-4 py-2 rounded-lg bg-azwara-medium text-white hover:bg-azwara-dark transition">

                <span x-show="!open">Lihat Pembahasan</span>
                <span x-show="open">Tutup Pembahasan</span>

            </button>

            <div x-show="open" x-collapse class="mt-4 border-t pt-4">

                <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">Jawaban benar:</h3>

                    <ul class="list-disc ml-6 text-gray-900 dark:text-gray-100">
                    @foreach ($q->options->where('is_correct', true) as $i => $opt)
                        <li>{!! $opt->option_text !!}</li>
                    @endforeach
                    </ul>

                <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">Pembahasan:</h3>

                <div class="prose dark:prose-invert max-w-none text-gray-800 dark:text-gray-100">
                    {!! $q->explanation !!}
                </div>

            </div>
        </div>


        {{-- ACTION BUTTONS --}}
        @role('admin|tentor')
        <div class="flex justify-end gap-3 mt-6 border-t pt-4">

            {{-- EDIT --}}
            <a href="{{ route('bank.question.edit', $q->id) }}"
                class="px-4 py-2 rounded-lg bg-azwara-medium/10
                    text-azwara-darker dark:text-azwara-lighter hover:bg-azwara-medium/20 transition">
                Edit
            </a>

            {{-- DELETE (SweetAlert) --}}
            <form method="POST"
                action="{{ route('bank.question.delete', $q->id) }}"
                class="sweet-confirm"
                data-message="Yakin ingin menghapus soal ini? Tindakan ini tidak dapat dibatalkan.">
                @csrf
                @method('DELETE')

                <button type="submit"
                    class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700">
                    Hapus
                </button>
            </form>

        </div>
        @endrole

    </div>
@empty
    <div class="text-center py-10 text-gray-500 dark:text-gray-400">
        <p class="text-lg font-semibold">Belum ada soal</p>
        <p class="text-sm mt-1">
            Silakan tambahkan soal untuk materi ini.
        </p>
    </div>
@endforelse

</div>


{{-- PAGINATION --}}
<div class="mt-6">
    {{ $questions->links() }}
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
    MathJax.typesetPromise();
});
</script>
@endpush
