<x-toggle-section title="🧪 Post Test">

    {{-- ================== BELUM ADA POST TEST ================== --}}
    @if(!$meeting->postTest)

        <div class="rounded-xl p-6
                    bg-white dark:bg-secondary
                    border border-dashed border-gray-300 dark:border-white/20
                    text-center space-y-4">

            <p class="text-gray-500 dark:text-gray-400">
                Post Test belum tersedia untuk pertemuan ini.
            </p>

            @role('admin|tentor')
                <form method="POST"
                      action="{{ route('posttest.store', $meeting) }}">
                    @csrf
                    <button class="px-6 py-3 rounded-xl
                                   bg-primary text-white
                                   hover:bg-primary/90
                                   font-semibold">
                        ➕ Buat Post Test
                    </button>
                </form>
            @endrole
        </div>

    @else
        @php
            $postTest = $meeting->postTest;
        @endphp

        {{-- ================== HEADER ================== --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between
                    gap-4 mb-6 mt-6">

            <div class="flex items-center gap-3">

                <div>

                    @if($postTest->status === 'inactive')
                        <span class="text-sm text-amber-600 font-medium">
                            ⏳ Belum dimulai
                        </span>
                    @elseif($postTest->status === 'active')
                        <span class="text-sm text-green-600 font-medium">
                            ▶️ Sedang berlangsung
                        </span>
                    @else
                        <span class="text-sm text-red-600 font-medium">
                            ✅ Selesai
                        </span>
                    @endif
                </div>
            </div>

        </div>

        {{-- ================== INFO CARDS ================== --}}
        <div class="grid grid-cols-2 gap-4 mb-6">

            <div class="rounded-xl p-4
                        bg-white dark:bg-secondary
                        border border-gray-200 dark:border-white/10">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                    Durasi
                </p>
                <p class="text-lg font-semibold text-gray-800 dark:text-white">
                    {{ $postTest->duration_minutes ?? '-' }} menit
                </p>
            </div>

            <div class="rounded-xl p-4
                        bg-white dark:bg-secondary
                        border border-gray-200 dark:border-white/10">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                    Jumlah Soal
                </p>
                <p class="text-lg font-semibold text-gray-800 dark:text-white">
                    {{ $postTest->questions->count() }} soal
                </p>
            </div>

        </div>

        {{-- ================== ACTIONS ================== --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap">

            {{-- ================== ADMIN / TENTOR ================== --}}
            @role('admin|tentor')

                @if($postTest->status === 'inactive')

                    <a href="{{ route('posttest.edit', $postTest) }}"
                       class="px-5 py-3 rounded-xl
                              bg-amber-500 text-white
                              hover:bg-amber-600
                              font-semibold text-center">
                        ✏️ Edit Post Test
                    </a>

                    <form method="POST"
                          action="{{ route('posttest.launch', $postTest) }}">
                        @csrf
                        <button class="px-5 py-3 rounded-xl
                                       bg-primary text-white
                                       hover:bg-primary/90
                                       font-semibold w-full sm:w-auto">
                            ▶️ Launch
                        </button>
                    </form>

                @elseif($postTest->status === 'active')

                    <form method="POST"
                          action="{{ route('posttest.close', $postTest) }}"
                          class="sweet-confirm"
                          data-message="Yakin ingin menutup post test?">
                        @csrf
                        <button class="px-5 py-3 rounded-xl
                                       bg-red-600 text-white
                                       hover:bg-red-700
                                       font-semibold w-full sm:w-auto">
                            ⛔ Close
                        </button>
                    </form>

                    <a href="{{ route('posttest.result.admin', $postTest) }}"
                        class="px-5 py-3 rounded-xl
                            bg-gray-200 dark:bg-gray-600
                            text-gray-800 dark:text-white
                            font-semibold
                            grid place-items-center">
                        📊 Lihat Hasil
                    </a>

                @else
                    <a href="{{ route('posttest.result.admin', $postTest) }}"
                       class="px-5 py-3 rounded-xl
                              bg-gray-200 dark:bg-gray-600
                              text-gray-800 dark:text-white
                              font-semibold text-center">
                        📘 Hasil & Pembahasan
                    </a>
                @endif

            @endrole

            {{-- ================== SISWA ================== --}}
            @role('siswa')

                @if($attempt && $attempt->is_submitted)

                    <div class="rounded-xl p-4
                                bg-green-50 dark:bg-green-900/20
                                border border-green-200 dark:border-green-500/30">
                        <p class="text-sm text-gray-700 dark:text-gray-200">
                            Skor Anda
                        </p>
                        <p class="text-2xl font-bold text-green-700 dark:text-green-400">
                            {{ $attempt->score }}
                        </p>
                    </div>

                    <a href="{{ route('posttest.result', $attempt) }}"
                        class="px-5 py-3 rounded-xl
                            bg-gray-200 dark:bg-gray-600
                            text-gray-800 dark:text-white
                            font-semibold
                            grid place-items-center">
                        📘 Lihat Hasil & Pembahasan
                    </a>

                @else

                    @if($postTest->status === 'active')

                        @if(!$attempt)
                            <form method="POST"
                                  action="{{ route('posttest.attempt.start', $postTest) }}">
                                @csrf
                                <button class="px-5 py-3 rounded-xl
                                               bg-primary text-white
                                               hover:bg-primary/90
                                               font-semibold w-full sm:w-auto">
                                    ▶️ Mulai Post Test
                                </button>
                            </form>
                        @else
                            <a href="{{ route('posttest.attempt.show', $attempt) }}"
                               class="px-5 py-3 rounded-xl
                                      bg-primary text-white
                                      hover:bg-primary/90
                                      font-semibold text-center">
                                ⏩ Lanjutkan Post Test
                            </a>
                        @endif

                    @else
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            Post test belum tersedia
                        </span>
                    @endif

                @endif

            @endrole

        </div>

    @endif

</x-toggle-section>
