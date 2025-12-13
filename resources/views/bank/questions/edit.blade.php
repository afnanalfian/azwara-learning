@extends('layouts.app')

@section('content')
<form method="POST"
      action="{{ route('bank.question.update', $question->id) }}"
      enctype="multipart/form-data"
      class="max-w-5xl mx-auto space-y-6">

    @csrf
    @method('PUT')

    {{-- HEADER --}}
    <div class="bg-white dark:bg-azwara-darker rounded-xl shadow p-6">
        <h1 class="text-2xl font-bold text-secondary dark:text-azwara-lighter">
            Edit Soal
        </h1>
        <p class="text-gray-600 dark:text-gray-300 mt-1">
            Perbarui isi soal, pembahasan, dan jawaban.
        </p>
    </div>

    {{-- TIPE SOAL --}}
    <div class="bg-white text-secondary dark:bg-azwara-darker dark:text-azwara-lighter rounded-xl shadow p-6 space-y-2">
        <label class="font-semibold">Tipe Soal</label>
        <select id="question-type" name="type"
                class="w-full rounded-lg border p-2
                       bg-white dark:bg-secondary/40
                       text-slate-800 dark:text-white">
                <option value="mcq" {{ $question->type === 'mcq' ? 'selected' : '' }}>
                    Pilihan Ganda (1 Benar)
                </option>
                <option value="mcma" {{ $question->type === 'mcma' ? 'selected' : '' }}>
                    Pilihan Ganda (Banyak Benar)
                </option>
                <option value="truefalse" {{ $question->type === 'truefalse' ? 'selected' : '' }}>
                    Benar / Salah
                </option>
        </select>
    </div>

    {{-- SOAL --}}
    <div class="bg-white text-secondary dark:bg-azwara-darker dark:text-azwara-lighter rounded-xl shadow p-6 space-y-4">
        <h2 class="text-lg font-semibold">Soal</h2>

        <textarea id="question-text"
                name="question_text"
                rows="4"
                class="w-full rounded-lg border p-3
                        bg-white dark:bg-secondary/30
                        text-slate-800 dark:text-white">
        {{ old('question_text', $question->question_text) }}
        </textarea>

        @if ($question->image)
            <img src="{{ asset('storage/' . $question->image) }}"
                class="max-h-40 rounded border mb-2">
        @endif

        <input type="file" name="question_image">

        <button type="button"
                class="btn-open-math px-4 py-2 bg-secondary text-white dark:bg-azwara-lighter dark:text-secondary rounded-lg"
                data-target="question-text">
            + Sisipkan Rumus
        </button>
        <div class="mt-4">
            <div class="text-sm font-semibold mb-1 opacity-70">
                Preview Soal
            </div>

            <div id="question-preview"
                class="prose dark:prose-invert
                        max-w-none
                        p-4 rounded-lg
                        bg-slate-50 dark:bg-secondary/30
                        border border-slate-200 dark:border-white/10">
                {!! $question->question_text ?: '<span class="opacity-50">Belum ada isi...</span>' !!}
            </div>
        </div>
    </div>

    {{-- OPSI JAWABAN --}}
    <div class="bg-white text-secondary dark:bg-azwara-darker dark:text-azwara-lighter rounded-xl shadow p-6 space-y-4">
        <h2 class="text-lg font-semibold">Opsi Jawaban</h2>

        <div id="options-wrapper" class="space-y-4 hidden"></div>

        <button type="button" id="add-option"
                class="hidden px-3 py-1 rounded bg-primary text-white">
            + Tambah Opsi
        </button>
    </div>

    {{-- PEMBAHASAN / EXPLANATION --}}
    <div class="bg-white text-secondary dark:bg-azwara-darker dark:text-azwara-lighter rounded-xl shadow p-6 space-y-4">
        <h2 class="text-lg font-semibold">Pembahasan</h2>

        <textarea id="explanation-text"
                name="explanation"
                rows="4"
                class="w-full rounded-lg border p-3">
        {{ old('explanation', $question->explanation) }}
        </textarea>

        <button type="button"
                class="btn-open-math px-4 py-2 bg-secondary text-white dark:bg-azwara-lighter dark:text-secondary rounded-lg"
                data-target="explanation-text">
            + Sisipkan Rumus
        </button>

        <div class="mt-4">
            <div class="text-sm font-semibold mb-1 opacity-70">
                Preview Pembahasan
            </div>

            <div id="explanation-preview"
                class="prose dark:prose-invert
                        max-w-none
                        p-4 rounded-lg
                        bg-slate-50 dark:bg-secondary/30
                        border border-slate-200 dark:border-white/10">
                {!! $question->explanation ?: '<span class="opacity-50">Belum ada isi...</span>' !!}
            </div>
        </div>
    </div>

    {{-- SUBMIT --}}
    <div class="flex justify-end gap-3">

        {{-- BATAL --}}
        <button type="submit"
                form="cancel-form"
                class="px-6 py-2 rounded-lg
                     bg-red-400  hover:bg-red-700
                    text-black hover:text-white">
            Batal
        </button>

        {{-- SIMPAN --}}
        <button type="submit"
                class="px-6 py-2 rounded-lg bg-azwara-darker text-white hover:bg-azwara-lighter hover:text-black">
            Perbarui Soal
        </button>

    </div>

</form>

{{-- FORM BATAL --}}
<form id="cancel-form"
      method="GET"
      action="{{ url()->previous() }}"
      class="sweet-confirm"
      data-message="Yakin ingin batal? Semua data soal yang anda buat akan hilang">
</form>

{{-- MATH MODAL --}}
<div id="math-modal"
     class="fixed inset-0 z-50 hidden
            bg-black/40">

    <div class="absolute bottom-6 left-1/2 -translate-x-1/2
                w-full max-w-xl
                bg-white dark:bg-secondary/90
                border border-slate-200 dark:border-white/10
                rounded-2xl shadow-xl
                p-5 space-y-4
                transition-all">

        <div class="flex justify-between items-center">
            <h3 class="font-semibold text-lg dark:text-white">
                ∑ Editor Rumus
            </h3>

            <button id="close-math-modal"
                    class="text-slate-500 hover:text-red-500">
                ✕
            </button>
        </div>

        <div id="math-editor"
             class="border rounded-lg p-3 min-h-[70px] text-lg
                    bg-white text-slate-800
                    dark:bg-secondary/30 dark:text-white
                    border-slate-300 dark:border-white/20
                    focus-within:ring-2 focus-within:ring-primary/40">
        </div>

        <div class="flex justify-between pt-2">
            <div class="flex gap-2">
                <button id="btn-confirm-math"
                        type="button"
                        class="px-4 py-1.5 rounded-lg
                               bg-primary text-white
                               hover:bg-azwara-darker">
                    Tambahkan
                </button>

                <button id="btn-cancel-math"
                        type="button"
                        class="px-4 py-1.5 rounded-lg
                               bg-slate-200 text-slate-700
                               dark:bg-white/10 dark:text-white">
                    Batal
                </button>
            </div>

            <button id="btn-open-docs"
                    type="button"
                    class="text-sm dark:text-white underline opacity-80">
                📘 Dokumentasi
            </button>
        </div>
    </div>
</div>

@include('layouts.partials.math_documentation')
@endsection

@push('styles')
<link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/mathquill/0.10.1/mathquill.min.css">
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/mathquill/0.10.1/mathquill.min.js"></script>

<script>
window.MathJax = {
    tex: { inlineMath: [['\\(', '\\)']] }
};
</script>
<script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {

    /* =====================
       MATHQUILL INIT
    ====================== */
    const MQ = MathQuill.getInterface(2);
    const mathField = MQ.MathField(
        document.getElementById('math-editor'),
        { spaceBehavesLikeTab: true }
    );

    const mathModal = document.getElementById('math-modal');
    let activeTextarea = null;

    /* =====================
       QUESTION PREVIEW
    ====================== */
    const questionInput  = document.getElementById('question-text');
    const previewBox     = document.getElementById('question-preview');

    function renderPreview() {
        if (!questionInput || !previewBox) return;

        if (!questionInput.value.trim()) {
            previewBox.innerHTML =
                '<span class="opacity-50">Belum ada isi...</span>';
            return;
        }

        previewBox.innerHTML = questionInput.value;
        MathJax.typesetPromise([previewBox]);
    }

    if (questionInput) {
        questionInput.addEventListener('input', renderPreview);
    }

    /* =====================
    EXPLANATION PREVIEW
    ===================== */
    const explanationInput  = document.getElementById('explanation-text');
    const explanationPreview = document.getElementById('explanation-preview');

    function renderExplanationPreview() {
        if (!explanationInput || !explanationPreview) return;

        if (!explanationInput.value.trim()) {
            explanationPreview.innerHTML =
                '<span class="opacity-50">Belum ada isi...</span>';
            return;
        }

        explanationPreview.innerHTML = explanationInput.value;
        MathJax.typesetPromise([explanationPreview]);
    }

    if (explanationInput) {
        explanationInput.addEventListener('input', renderExplanationPreview);
    }

    /* =====================
       OPEN MATH MODAL
    ====================== */
    document.addEventListener('click', e => {

        if (e.target.classList.contains('btn-open-math')) {

            // Target dari soal
            if (e.target.dataset.target) {
                activeTextarea =
                    document.getElementById(e.target.dataset.target);
            }
            // Target dari opsi
            else {
                activeTextarea = e.target
                    .closest('.option-item')
                    ?.querySelector('textarea');
            }

            if (!activeTextarea) return;

            mathModal.classList.remove('hidden');
            mathField.focus();
        }
    });

    /* =====================
       CONFIRM MATH
    ====================== */
    document.getElementById('btn-confirm-math').onclick = () => {
        if (!activeTextarea) return;

        activeTextarea.value += ` \\(${mathField.latex()}\\) `;
        mathField.latex('');
        closeMathModal();
        renderPreview();
    };

    /* =====================
       CLOSE MODAL
    ====================== */
    function closeMathModal() {
        mathModal.classList.add('hidden');
        mathField.latex('');
        activeTextarea = null;
    }

    document.getElementById('btn-cancel-math')
        .onclick = closeMathModal;

    document.getElementById('close-math-modal')
        .onclick = closeMathModal;

    /* =====================
       QUESTION TYPE & OPTIONS
    ====================== */
    const typeSelect     = document.getElementById('question-type');
    const optionsWrapper = document.getElementById('options-wrapper');
    const addBtn         = document.getElementById('add-option');

    let optionIndex = 0;

    typeSelect?.addEventListener('change', () => {
        optionsWrapper.innerHTML = '';
        optionsWrapper.classList.add('hidden');
        addBtn.classList.add('hidden');
        optionIndex = 0;

        if (['mcq','mcma'].includes(typeSelect.value)) {
            addOption();
            addOption();
            addBtn.classList.remove('hidden');
        }

        if (typeSelect.value === 'truefalse') {
            renderTrueFalse();
        }
    });

    addBtn?.addEventListener('click', addOption);

    function addOption() {
        const isMcq = typeSelect.value === 'mcq';
        optionsWrapper.classList.remove('hidden');

        optionsWrapper.insertAdjacentHTML('beforeend', `
        <div class="option-item flex gap-3 items-start">

            <input type="${isMcq ? 'radio' : 'checkbox'}"
                   name="correct${isMcq ? '' : '[]'}"
                   value="${optionIndex}"
                   class="mt-3">

            <div class="flex-1 space-y-2">

                <textarea name="options[]"
                    class="option-text w-full rounded-lg border p-2
                           bg-white dark:bg-secondary/30
                           text-slate-800 dark:text-white"
                    placeholder="Teks opsi..."></textarea>

                <input type="file" name="option_images[]"
                       class="block text-sm">

                <div class="flex gap-3 text-xs">
                    <button type="button"
                            class="btn-open-math underline">
                        + Rumus
                    </button>

                    <button type="button"
                            class="btn-remove-option text-red-500">
                        Hapus
                    </button>
                </div>
            </div>
        </div>
        `);

        optionIndex++;
        updateRemoveButtons();
    }

    function updateRemoveButtons() {
        const items = optionsWrapper.querySelectorAll('.option-item');
        const canRemove = items.length > 2;

        optionsWrapper
            .querySelectorAll('.btn-remove-option')
            .forEach(btn => btn.disabled = !canRemove);
    }

    document.addEventListener('click', e => {
        if (e.target.classList.contains('btn-remove-option')) {
            e.target.closest('.option-item')?.remove();
            updateRemoveButtons();
        }
    });

    function renderTrueFalse() {
        optionsWrapper.classList.remove('hidden');
        optionsWrapper.innerHTML = `
        <label class="flex gap-2">
            <input type="radio" name="truefalse_correct[0]" value="1">
            Benar
        </label>
        <label class="flex gap-2">
            <input type="radio" name="truefalse_correct[0]" value="0">
            Salah
        </label>
        `;
    }

    const EXISTING_QUESTION = @json($question);
    /* =====================
    LOAD EXISTING DATA
    ===================== */
    if (EXISTING_QUESTION) {

        // set type
        typeSelect.value = EXISTING_QUESTION.type;

        // isi opsi
        if (['mcq', 'mcma'].includes(EXISTING_QUESTION.type)) {

            optionsWrapper.classList.remove('hidden');
            addBtn.classList.remove('hidden');

            EXISTING_QUESTION.options.forEach((opt, i) => {
                addOption();

                const item = optionsWrapper.children[i];
                const textarea = item.querySelector('textarea');
                const input = item.querySelector('input[type=radio], input[type=checkbox]');

                textarea.value = opt.option_text;
                input.checked = opt.is_correct;
            });

        }

        // true false
        if (EXISTING_QUESTION.type === 'truefalse') {
            renderTrueFalse();

            EXISTING_QUESTION.options.forEach(opt => {
                const val = opt.option_text === 'Benar' ? 1 : 0;
                if (opt.is_correct) {
                    document.querySelector(
                        `input[name="truefalse_correct[0]"][value="${val}"]`
                    ).checked = true;
                }
            });
        }

        // render preview awal
        renderPreview();
        renderExplanationPreview();
    }

});
// sidebar docs
const docs = document.getElementById('math-docs');
document.getElementById('btn-open-docs').onclick =
    () => docs.classList.remove('translate-x-full');
document.getElementById('close-docs').onclick =
    () => docs.classList.add('translate-x-full');
</script>
@endpush

