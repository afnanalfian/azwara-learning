{{-- ================= SOAL TERPILIH ================= --}}
<div class="bg-azwara-lightest dark:bg-secondary/80
            rounded-2xl p-6
            border border-azwara-light/30 dark:border-white/10 space-y-4">

    {{-- HEADER --}}
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
            Soal Post Test
        </h2>

        @if($postTest->status === 'inactive')
            <button
                @click="openAddQuestion = true"
                class="px-4 py-2 rounded-lg bg-primary text-white hover:bg-primary/90">
                Tambah Soal
            </button>
        @endif
    </div>

    @if($postTest->questions->isEmpty())
        <p class="text-gray-500 dark:text-gray-400">
            Belum ada soal dipilih.
        </p>
    @else

        {{-- ================= SORTABLE WRAPPER ================= --}}
        <div class="space-y-6">
            @foreach ($postTest->questions as $i => $pq)
                @php $q = $pq->question; @endphp

                {{-- ================= ITEM ================= --}}
                <div
                    data-id="{{ $pq->id }}"
                    class="relative bg-azwara-lightest dark:bg-secondary
                           border border-gray-200 dark:border-white/10 dark:text-white
                           rounded-xl p-6 shadow-sm">

                    {{-- HEADER --}}
                    <div class="flex items-center justify-between mb-4">
                        <h3
                            data-question-number
                            class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            Soal {{ $i + 1 }}
                        </h3>

                        <div class="flex items-center gap-3">

                            <span class="px-3 py-1.5 text-sm rounded-lg
                                         bg-primary/10 text-primary font-semibold">
                                {{ strtoupper($q->type) }}
                            </span>

                            @if($postTest->status === 'inactive')
                                <form method="POST"
                                      action="{{ route('posttest.questions.detach', [$postTest, $q]) }}"
                                      class="sweet-confirm"
                                      data-message="Hapus soal ini?">
                                    @csrf
                                    @method('DELETE')

                                    <button class="text-red-600 hover:underline text-sm">
                                        Hapus
                                    </button>
                                </form>
                            @endif

                        </div>
                    </div>

                    {{-- GAMBAR SOAL --}}
                    @if ($q->image)
                        <img
                            src="{{ Storage::url($q->image) }}"
                            alt="Gambar Soal"
                            class="max-h-[320px] mx-auto rounded-xl shadow
                                object-contain bg-azwara-lightest dark:bg-gray-800 p-2">
                    @endif

                    {{-- TEKS SOAL --}}
                    <div class="prose dark:prose-invert max-w-none mb-4">
                        {!! $q->question_text !!}
                    </div>

                    {{-- OPSI JAWABAN --}}
                    <div class="space-y-3">
                        @foreach ($q->options as $opt)
                            <div class="border border-black rounded-lg px-4 py-3
                                        bg-azwara-lightest dark:bg-white/5
                                        text-gray-800 dark:text-gray-100">

                                {{-- LABEL + TEKS (SATU BARIS) --}}
                                <div class="flex items-start gap-2">

                                    @if ($q->type !== 'truefalse')
                                        <span class="font-semibold mt-1 shrink-0">
                                            {{ $opt->label }}.
                                        </span>
                                    @endif

                                    <div class="prose dark:prose-invert max-w-none">
                                        {!! $opt->option_text !!}
                                    </div>
                                </div>

                                {{-- GAMBAR OPSI (DI BAWAH TEKS) --}}
                                @if ($opt->image)
                                    <div class="mt-3">
                                        <img
                                            src="{{ Storage::url($opt->image) }}"
                                            alt="Gambar opsi"
                                            class="max-h-48 rounded-lg border bg-white p-2 object-contain">
                                    </div>
                                @endif

                            </div>
                        @endforeach
                    </div>

                </div>
            @endforeach
        </div>
    @endif
</div>
