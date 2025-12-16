<x-toggle-section title="📚 Materi">


    {{-- ======================
        BELUM ADA MATERI
    ======================= --}}
    @if(!$meeting->material)

        <div class="text-sm text-gray-600 dark:text-gray-400 mt-3">
            Materi belum ditambahkan.
        </div>

        @role('admin|tentor')
        <form method="POST"
              action="{{ route('meeting.material.store', $meeting) }}"
              enctype="multipart/form-data"
              class="mt-4 space-y-4">

            @csrf

            <div>
                <input type="file"
                       name="material"
                       accept="application/pdf"
                       required
                       class="block w-full text-sm
                              text-gray-600 dark:text-gray-300
                              file:mr-4 file:px-4 file:py-2
                              file:rounded-lg file:border-0
                              file:bg-primary file:text-white
                              hover:file:bg-primary/90">
            </div>

            <button type="submit"
                    class="inline-flex items-center gap-2
                           px-4 py-2 rounded-lg
                           bg-primary text-white text-sm font-medium
                           hover:bg-primary/90 transition">
                Upload Materi
            </button>
        </form>
        @endrole

    {{-- ======================
        SUDAH ADA MATERI
    ======================= --}}
    @else

    {{-- PREVIEW --}}

    {{-- MOBILE --}}
    <div class="sm:hidden
                flex items-center justify-between gap-4
                p-4 mt-5 rounded-xl
                bg-gray-50 dark:bg-secondary
                border border-azwara-light/30 dark:border-white/10">

        <div>
            <div class="text-sm font-medium text-gray-700 dark:text-gray-200">
                Materi PDF
            </div>
        </div>

        <a href="{{ asset('storage/'.$meeting->material->file_path) }}"
        target="_blank"
        class="shrink-0 px-4 py-2 rounded-lg
                bg-primary text-white text-sm font-medium ">
            Buka
        </a>
    </div>

    {{-- DESKTOP --}}
    <div class="hidden sm:block mt-5
                w-full overflow-hidden rounded-xl border
                border-azwara-light/30 dark:border-white/10">
        <iframe
            src="{{ asset('storage/'.$meeting->material->file_path) }}"
            class="w-full h-[500px]">
        </iframe>
    </div>

        {{-- ACTIONS --}}
        <div class="mt-4 flex flex-col sm:flex-row
                    sm:items-center sm:justify-between
                    gap-3">

            {{-- BUKA FILE (SEMUA ROLE) --}}
            <a href="{{ asset('storage/'.$meeting->material->file_path) }}"
               target="_blank"
               class="inline-flex justify-center items-center gap-2 hidden sm:block
                      px-4 py-2 rounded-lg
                      text-sm font-medium
                      bg-gray-100 dark:bg-secondary
                      text-gray-700 dark:text-gray-200
                      hover:bg-gray-200 dark:hover:bg-secondary/80
                      transition">
                Buka File
            </a>

            {{-- ADMIN / TENTOR --}}
            @role('admin|tentor')
            <div class="flex flex-col sm:flex-row gap-3">

                {{-- GANTI MATERI --}}
                <form method="POST"
                      action="{{ route('meeting.material.store', $meeting) }}"
                      enctype="multipart/form-data"
                      class="flex flex-col sm:flex-row gap-2">

                    @csrf

                    <input type="file"
                           name="material"
                           accept="application/pdf"
                           required
                           class="block w-full sm:w-auto text-sm
                                  text-gray-600 dark:text-gray-300
                                  file:mr-4 file:px-4 file:py-2
                                  file:rounded-lg file:border-0
                                  file:bg-primary file:text-white
                                  hover:file:bg-primary/90">

                    <button type="submit"
                            class="px-4 py-2 rounded-lg
                                   bg-primary text-white text-sm font-medium
                                   hover:bg-primary/90 transition">
                        Ganti
                    </button>
                </form>

                {{-- HAPUS --}}
                <form method="POST"
                      action="{{ route('meeting.material.destroy', $meeting) }}"
                      class="sweet-confirm"
                      data-message="Yakin ingin menghapus materi ini?">
                    @csrf
                    @method('DELETE')

                    <button
                        class="px-4 py-2 rounded-lg
                               bg-red-600 text-white text-sm font-medium
                               hover:bg-red-700 transition">
                        Hapus
                    </button>
                </form>

            </div>
            @endrole
        </div>

    @endif

</x-toggle-section>
