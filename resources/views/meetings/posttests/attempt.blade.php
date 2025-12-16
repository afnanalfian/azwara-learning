@extends('layouts.exam')

@section('content')
<div class="h-full flex flex-col">

        {{-- ================= TIMER ================= --}}
        <div class="p-4 bg-secondary text-white text-center">
            Sisa Waktu:
            <span
                id="timer"
                data-remaining="{{ $attempt->remaining_seconds }}"
                class="font-bold text-lg">
            </span>
        </div>

        <div class="flex flex-1 overflow-hidden">

        {{-- ================= NAVIGATOR ================= --}}
        <aside class="w-24 bg-white dark:bg-secondary border-r border-gray-200 dark:border-white/10
                      overflow-y-auto p-2">
            <div class="grid grid-cols-2 gap-2">
                @foreach($attempt->postTest->questions as $i => $pq)
                    @php
                        $answered = $attempt->answers
                            ->where('question_id', $pq->question_id)
                            ->isNotEmpty();
                    @endphp

                    <button
                        type="button"
                        class="nav-btn
                               w-full py-2 rounded text-sm font-semibold text-white
                               {{ $answered ? 'bg-green-600' : 'bg-red-600' }}"
                        data-index="{{ $i }}">
                        {{ $i + 1 }}
                    </button>
                @endforeach
            </div>
        </aside>

        {{-- ================= QUESTION AREA ================= --}}
        <main class="flex-1 p-6 overflow-y-auto">

            @foreach($attempt->postTest->questions as $i => $pq)
                @php
                    $question = $pq->question;
                    $answer = $attempt->answers
                        ->where('question_id', $question->id)
                        ->first();
                    $selected = $answer?->selected_options ?? [];
                @endphp

                <div class="question-slide {{ $i === 0 ? '' : 'hidden' }}"
                     data-index="{{ $i }}"
                     data-question-id="{{ $question->id }}">

                    <div class="mb-6">
                        <h2 class="text-lg font-semibold mb-2">
                            Soal {{ $i + 1 }}
                        </h2>

                        <div class="prose dark:prose-invert max-w-none">
                            {!! $question->question_text !!}
                        </div>
                    </div>

                    {{-- OPTIONS --}}
                    <div class="space-y-3">
                        @foreach($question->options as $option)
                            <label class="flex items-start gap-3 p-3 rounded-lg
                                          border border-gray-200 dark:border-white/10
                                          cursor-pointer hover:bg-gray-100 dark:hover:bg-white/5">

                                <input
                                    type="{{ $question->type === 'mcma' ? 'checkbox' : 'radio' }}"
                                    name="question_{{ $question->id }}[]"
                                    value="{{ $option->id }}"
                                    @checked(in_array($option->id, $selected))
                                    class="answer-input mt-1"
                                >

                                <div class="prose dark:prose-invert max-w-none text-sm">
                                    {!! $option->option_text !!}
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach

        </main>
    </div>

    {{-- ================= ACTION ================= --}}
    <div class="p-4 bg-white dark:bg-secondary border-t border-gray-200 dark:border-white/10
                flex justify-between items-center">

        <button id="prevBtn"
                class="px-4 py-2 rounded-lg bg-gray-200 dark:bg-white/10">
            Sebelumnya
        </button>

        <form id="auto-submit-form"
              method="POST"
              action="{{ route('posttest.submit', $attempt) }}"
              class="sweet-confirm"
              data-message="Yakin ingin mengakhiri post test?">
            @csrf
            <button class="px-5 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700">
                Submit
            </button>
        </form>

        <button id="nextBtn"
                class="px-4 py-2 rounded-lg bg-primary text-white hover:bg-primary/90">
            Selanjutnya
        </button>
    </div>

</div>
@endsection
@push('script')
{{-- ================= JS ================= --}}
<script>
/* ================= TIMER ================= */

const timerEl = document.getElementById('timer');
let remaining = parseInt(timerEl.dataset.remaining, 10);

function formatTime(seconds) {
    const m = Math.floor(seconds / 60);
    const s = seconds % 60;
    return `${m}:${s.toString().padStart(2, '0')}`;
}

// render awal
timerEl.innerText = formatTime(remaining);

const countdown = setInterval(() => {
    if (remaining <= 0) {
        clearInterval(countdown);

        // auto submit
        document.getElementById('auto-submit-form')?.submit();
        return;
    }

    remaining--;
    timerEl.innerText = formatTime(remaining);
}, 1000);

/* ================= QUESTION NAV ================= */
let currentIndex = 0;
const slides = document.querySelectorAll('.question-slide');
const navButtons = document.querySelectorAll('.nav-btn');

function showQuestion(index) {
    slides.forEach(s => s.classList.add('hidden'));
    slides[index].classList.remove('hidden');
    currentIndex = index;
}

navButtons.forEach(btn => {
    btn.addEventListener('click', () => {
        showQuestion(parseInt(btn.dataset.index));
    });
});

document.getElementById('prevBtn').onclick = () => {
    if (currentIndex > 0) showQuestion(currentIndex - 1);
};

document.getElementById('nextBtn').onclick = () => {
    if (currentIndex < slides.length - 1) showQuestion(currentIndex + 1);
};

/* ================= SAVE ANSWER (AJAX) ================= */
document.querySelectorAll('.answer-input').forEach(input => {
    input.addEventListener('change', () => {

        const slide = input.closest('.question-slide');
        const questionId = slide.dataset.questionId;

        const checked = slide.querySelectorAll('.answer-input:checked');
        let selected = [];

        checked.forEach(i => selected.push(i.value));

        fetch("{{ route('posttest.answer.save', $attempt) }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify({
                question_id: questionId,
                selected_options: selected
            })
        });

        // update nav color
        const nav = document.querySelector(
            `.nav-btn[data-index="${slide.dataset.index}"]`
        );
        nav.classList.remove('bg-red-600');
        nav.classList.add('bg-green-600');
    });
});
</script>
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
