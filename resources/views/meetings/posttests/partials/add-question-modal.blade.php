<div
    x-show="openAddQuestion"
    x-cloak
    class="absolute inset-0 z-50 flex items-center justify-center
           bg-black/40 backdrop-blur-sm">

<div
    x-show="openAddQuestion"
    x-cloak
    class="absolute inset-0 z-50 flex items-center justify-center
           bg-black/40 backdrop-blur-sm">

    <div
        x-data="postTestQuestionPicker({
            postTestId: {{ $postTest->id }},
            usedIds: @js($usedQuestionIds ?? [])
        })"
        @click.outside="openAddQuestion = false"
        class="bg-azwara-lightest dark:bg-secondary
               w-full max-w-6xl
               max-h-[90vh]
               rounded-2xl shadow-xl
               flex flex-col overflow-hidden">

        {{-- HEADER --}}
        <div class="px-6 py-4 border-b dark:border-white/10 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                Tambah Soal Post Test
            </h3>
            <button @click="openAddQuestion = false" class="text-xl dark:text-white">&times;</button>
        </div>

        {{-- FILTER --}}
        <div class="px-6 py-4 border-b dark:border-white/10
                    grid grid-cols-1 md:grid-cols-3 gap-4">

            {{-- CATEGORY --}}
            <div
                x-data="{
                    open:false,
                    categories: @js($categories->map(fn($c)=>['id'=>$c->id,'name'=>$c->name]))
                }"
                class="relative">

                <button
                    @click="open=!open"
                    class="w-full text-left px-3 py-2 text-sm rounded-lg
                        border bg-white text-gray-800
                        dark:bg-slate-800 dark:text-gray-100
                        dark:border-white/10">

                    <span x-text="
                        categoryId
                        ? categories.find(c => c.id == categoryId)?.name
                        : 'Pilih Kategori'
                    "></span>
                </button>

                <div x-show="open" @click.outside="open=false"
                    class="absolute z-50 mt-1 w-full max-h-48 overflow-y-auto
                        bg-white dark:bg-slate-800
                        border dark:border-white/10 rounded-lg">

                    <template x-for="c in categories" :key="c.id">
                        <div
                            @click="
                                categoryId=c.id;
                                open=false;
                                loadMaterials();
                            "
                            class="px-3 py-2 text-sm cursor-pointer
                                text-gray-800 dark:text-gray-100
                                hover:bg-gray-100 dark:hover:bg-white/10"
                            x-text="c.name">
                        </div>
                    </template>
                </div>
            </div>

            {{-- MATERIAL --}}
            <div x-data="{ open:false }" class="relative">

                <button
                    :disabled="!materials.length"
                    @click="if(materials.length) open=!open"
                    class="w-full text-left px-3 py-2 text-sm rounded-lg
                        border bg-white text-gray-800
                        disabled:opacity-50
                        dark:bg-slate-800 dark:text-gray-100
                        dark:border-white/10">

                    <span x-text="
                        materialId
                        ? materials.find(m => m.id == materialId)?.name
                        : 'Pilih Materi'
                    "></span>
                </button>

                <div x-show="open" @click.outside="open=false"
                    class="absolute z-50 mt-1 w-full max-h-48 overflow-y-auto
                        bg-white dark:bg-slate-800
                        border dark:border-white/10 rounded-lg">

                    <template x-for="m in materials" :key="m.id">
                        <div
                            @click="
                                materialId=m.id;
                                open=false;
                                fetchQuestions(1);
                            "
                            class="px-3 py-2 text-sm cursor-pointer
                                text-gray-800 dark:text-gray-100
                                hover:bg-gray-100 dark:hover:bg-white/10"
                            x-text="m.name">
                        </div>
                    </template>
                </div>
            </div>

            {{-- TYPE --}}
            <div x-data="{ open:false }" class="relative">

                <button
                    :disabled="!materialId"
                    @click="if(materialId) open=!open"
                    class="w-full text-left px-3 py-2 text-sm rounded-lg
                        border bg-white text-gray-800
                        disabled:opacity-50
                        dark:bg-slate-800 dark:text-gray-100
                        dark:border-white/10">

                    <span x-text="
                        type
                        ? type.toUpperCase()
                        : 'Semua Tipe'
                    "></span>
                </button>

                <div x-show="open" @click.outside="open=false"
                    class="absolute z-50 mt-1 w-full max-h-48 overflow-y-auto
                        bg-white dark:bg-slate-800
                        border dark:border-white/10 rounded-lg">

                    <template x-for="t in [
                        {v:'', l:'Semua Tipe'},
                        {v:'mcq', l:'MCQ'},
                        {v:'mcma', l:'MCMA'},
                        {v:'truefalse', l:'True / False'}
                    ]" :key="t.v">
                        <div
                            @click="
                                type=t.v;
                                open=false;
                                fetchQuestions(1);
                            "
                            class="px-3 py-2 text-sm cursor-pointer
                                text-gray-800 dark:text-gray-100
                                hover:bg-gray-100 dark:hover:bg-white/10"
                            x-text="t.l">
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- LIST --}}
        <div class="flex-1 overflow-y-auto p-6 space-y-4">

            <template x-if="!materialId">
                <p class="text-center text-gray-500">
                    Pilih kategori & materi terlebih dahulu
                </p>
            </template>

            <template x-for="q in questions" :key="q.id">
                <label
                    class="block p-4 rounded-xl border
                           hover:bg-gray-50 dark:text-white dark:hover:bg-white/5
                           cursor-pointer">

                    <div class="flex gap-3">
                        <input type="checkbox"
                               :value="q.id"
                               x-model="selected">

                        <div class="flex-1">

                            <div class="flex justify-between mb-2">
                                <span class="font-semibold">Soal</span>
                                <span class="text-xs px-2 py-1 rounded bg-primary/10 text-primary">
                                    <span x-text="q.type.toUpperCase()"></span>
                                </span>
                            </div>

                            <template x-if="q.question_image">
                                <img :src="q.image_url"
                                     class="max-h-48 mx-auto mb-3 rounded">
                            </template>

                            <div class="prose dark:prose-invert max-w-none"
                                 x-html="q.question_text"></div>

                        </div>
                    </div>
                </label>
            </template>

            <template x-if="questions.length === 0 && materialId">
                <p class="text-center text-gray-500 dark:text-white">
                    Tidak ada soal tersedia
                </p>
            </template>
        </div>
        {{-- PAGINATION --}}
        <div class="flex justify-between items-center pt-4"
            x-show="pagination.last_page > 1">

            <button
                @click="fetchQuestions(pagination.current_page - 1)"
                :disabled="pagination.current_page === 1"
                class="px-3 py-1 rounded bg-gray-200 dark:bg-white/10">
                Prev
            </button>

            <span class="text-sm text-gray-600 dark:text-gray-300">
                Page <span x-text="pagination.current_page"></span>
                of <span x-text="pagination.last_page"></span>
            </span>

            <button
                @click="fetchQuestions(pagination.current_page + 1)"
                :disabled="pagination.current_page === pagination.last_page"
                class="px-3 py-1 rounded bg-gray-200 dark:bg-white/10">
                Next
            </button>
        </div>

        {{-- FOOTER --}}
        <div class="px-6 py-4 border-t dark:border-white/10 flex justify-end gap-3">

            <button @click="openAddQuestion = false"
                    class="px-4 py-2 rounded-lg dark:text-white">
                Batal
            </button>

            <form method="POST"
                  action="{{ route('posttest.questions.attach', $postTest) }}">
                @csrf
                <template x-for="id in selected">
                    <input type="hidden" name="question_ids[]" :value="id">
                </template>

                <button type="submit"
                        class="px-5 py-2 rounded-lg bg-primary text-white"
                        :disabled="selected.length === 0">
                    Tambahkan Soal
                </button>
            </form>

        </div>
    </div>
</div>
