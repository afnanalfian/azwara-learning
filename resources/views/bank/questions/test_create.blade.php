@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    {{-- HEADER --}}
    <div class="bg-white dark:bg-secondary/30 rounded-xl shadow p-6">
        <h1 class="text-2xl font-bold text-secondary dark:text-white">
            Math Editor Full Testing
        </h1>
        <p class="text-gray-600 dark:text-gray-300 mt-1">
            Tulis teks biasa, lalu sisipkan rumus matematika secara real-time.
        </p>
    </div>

    {{-- SOAL --}}
    <div class="bg-white dark:bg-secondary/30 rounded-xl shadow p-6 space-y-4">
        <h2 class="text-lg font-semibold">Soal</h2>

        {{-- TEXT AREA --}}
        <textarea id="question-text"
                  rows="4"
                  class="w-full border rounded-lg p-3 focus:ring-primary focus:border-primary"
                  placeholder="Contoh: Diketahui x adalah 15. Berapakah hasil dari ..."></textarea>

        {{-- BUTTON --}}
        <button id="btn-insert-math"
                class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90">
            + Sisipkan Rumus
        </button>

        {{-- MATHQUILL BOX --}}
        <div id="math-box"
             class="hidden border rounded-lg p-4 bg-gray-50 space-y-3">
            <div class="text-sm font-medium text-gray-600">
                Editor Rumus (MathQuill)
            </div>

            <div id="math-editor"
                 class="border rounded-lg p-3 bg-white text-lg min-h-[60px]">
            </div>

            <div class="flex gap-2 justify-between">
                <div class="flex gap-2">
                    <button id="btn-add-math"
                            class="px-4 py-1 bg-secondary text-white rounded">
                        Tambahkan
                    </button>
                    <button id="btn-cancel-math"
                            class="px-4 py-1 bg-gray-400 text-white rounded">
                        Batal
                    </button>
                </div>

                <button id="btn-open-docs"
                        class="px-3 py-1 border rounded text-sm
                            hover:bg-primary hover:text-white">
                    Lihat Dokumentasi
                </button>
            </div>
        </div>

        {{-- FINAL OUTPUT --}}
        <input type="hidden" id="final_question">

        <div class="text-sm text-gray-500">
            <strong>Output (DB-ready):</strong>
            <code id="latex-output" class="block mt-1"></code>
        </div>
    </div>

    {{-- PREVIEW --}}
    <div class="bg-white dark:bg-secondary/30 rounded-xl shadow p-6 space-y-2">
        <h2 class="text-lg font-semibold">Preview (Seperti Siswa)</h2>

        <div id="preview"
             class="prose max-w-none bg-gray-50 p-4 rounded-lg">
            —
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
{{-- jQuery (WAJIB untuk MathQuill) --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

{{-- MathQuill --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/mathquill/0.10.1/mathquill.min.js"></script>

{{-- MathJax --}}
<script>
window.MathJax = {
    tex: {
        inlineMath: [['\\(', '\\)']]
    }
};
</script>
<script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const MQ = MathQuill.getInterface(2);
    const mathField = MQ.MathField(
        document.getElementById('math-editor'),
        { spaceBehavesLikeTab: true }
    );

    const textarea = document.getElementById('question-text');
    const finalInput = document.getElementById('final_question');
    const output = document.getElementById('latex-output');
    const preview = document.getElementById('preview');

    const mathBox = document.getElementById('math-box');

    document.getElementById('btn-insert-math').onclick = () => {
        mathBox.classList.remove('hidden');
        mathField.focus();
    };

    document.getElementById('btn-cancel-math').onclick = () => {
        mathBox.classList.add('hidden');
        mathField.latex('');
    };

    document.getElementById('btn-add-math').onclick = () => {
        const latex = mathField.latex();
        textarea.value += ` \\( ${latex} \\) `;
        sync();
        mathField.latex('');
        mathBox.classList.add('hidden');
    };

    textarea.addEventListener('input', sync);

    function sync() {
        finalInput.value = textarea.value;
        output.innerText = textarea.value;
        preview.innerHTML = textarea.value;
        MathJax.typesetPromise([preview]);
    }

});
const docs = document.getElementById('math-docs');

document.getElementById('btn-open-docs').onclick = () => {
    docs.classList.remove('translate-x-full');
};

document.getElementById('close-docs').onclick = () => {
    docs.classList.add('translate-x-full');
};
</script>
@endpush
