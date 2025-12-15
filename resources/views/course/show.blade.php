@extends('layouts.app')

@section('content')
<div class="space-y-8">
    {{-- COURSE TITLE (TOP) --}}
    <div>
        <h1 class="text-3xl font-extrabold
                text-azwara-darker dark:text-azwara-lighter">
            {{ $course->name }}
        </h1>
    </div>
    {{-- =========================
        COURSE HEADER + ACTION
    ========================== --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4
                rounded-2xl p-6
                bg-white/80 dark:bg-secondary/80
                border border-azwara-light/30 dark:border-white/10
                backdrop-blur">

        {{-- LEFT --}}
        <div>
            <h1 class="text-2xl font-bold text-azwara-darker dark:text-azwara-lighter">
                {{ $course->title }}
            </h1>

            <div class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                Tentor:
                @foreach ($course->teachers as $teacher)
                    <span class="font-medium">
                        {{ $teacher->user->name }}
                    </span>{{ !$loop->last ? ',' : '' }}
                @endforeach
            </div>

            <div class="mt-3 text-sm text-gray-500 dark:text-gray-400">
                Total Pertemuan:
                <span class="font-semibold text-gray-700 dark:text-gray-200">
                    {{ $course->meetings->count() }}
                </span>
            </div>
        </div>

        {{-- RIGHT ACTION --}}
        <div class="flex items-center gap-3">

            {{-- SEARCH --}}
            <input
                id="meeting-search"
                type="text"
                placeholder="Cari pertemuan..."
                class="w-64 rounded-lg border-gray-300
                       bg-white dark:bg-secondary
                       dark:border-white/10
                       dark:text-white
                       focus:ring-primary focus:border-primary">

            {{-- ADD MEETING --}}
            @if(auth()->user()->hasRole(['admin','tentor']))
                <a href="{{ route('meeting.create', $course) }}"
                   class="inline-flex items-center gap-2
                          px-4 py-2 rounded-lg
                          bg-primary text-white
                          hover:bg-primary/90
                          transition">
                    + Tambah Pertemuan
                </a>
            @endif
        </div>
    </div>

    {{-- =========================
        MEETINGS LIST
    ========================== --}}
    <div id="meeting-list" class="grid gap-4">

        @forelse ($course->meetings as $index => $meeting)
            <a href="{{ route('meeting.show', $meeting) }}">
            <div
                class="meeting-card group rounded-xl p-5
                       bg-white dark:bg-secondary/70
                       border border-azwara-light/30 dark:border-white/10
                       hover:shadow-lg hover:-translate-y-0.5
                       transition-all duration-300">

                <div class="flex items-center justify-between gap-4">

                    {{-- LEFT --}}
                    <div class="space-y-1">
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            Pertemuan {{ $index + 1 }}
                        </div>

                        <h3 class="meeting-title text-lg font-semibold
                                   text-azwara-darker dark:text-azwara-lighter">
                            {{ $meeting->title }}
                        </h3>

                        @if ($meeting->scheduled_at)
                            <div class="text-sm text-gray-600 dark:text-gray-300">
                                {{ $meeting->scheduled_at->timezone('Asia/Jakarta')->translatedFormat('l, d F Y • H:i') }} WIB
                            </div>
                        @endif
                    </div>

                    {{-- RIGHT --}}
                    <div class="flex items-center gap-4">

                        {{-- STATUS --}}
                        @php
                            $statusColor = match ($meeting->status) {
                                'upcoming' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300',
                                'live'     => 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-300',
                                'done'     => 'bg-gray-200 text-gray-700 dark:bg-gray-500/20 dark:text-gray-300',
                                default    => 'bg-gray-100 text-gray-600 dark:bg-gray-500/10 dark:text-gray-400',
                            };
                        @endphp

                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $statusColor }}">
                            {{ ucfirst($meeting->status) }}
                        </span>

                        {{-- =====================
                            JOIN ZOOM
                        ====================== --}}
                        @if (empty($meeting->zoom_link))
                            <button
                                disabled
                                class="px-4 py-2 rounded-lg text-sm font-medium
                                    bg-gray-300 dark:bg-gray-600
                                    text-gray-600 dark:text-gray-300
                                    cursor-not-allowed">
                                Belum ada link Zoom
                            </button>
                        @else
                            <a href="{{ route('meeting.joinZoom', $meeting) }}"
                            class="px-4 py-2 rounded-lg text-sm font-medium
                                    bg-primary text-white
                                    hover:bg-primary/90 transition">
                                Join Zoom
                            </a>
                        @endif

                        {{-- =====================
                            ADMIN / TENTOR ACTION
                        ====================== --}}
                        @if(auth()->user()->hasRole(['admin','tentor']))

                            {{-- CANCEL MEETING --}}
                            @if($meeting->status !== 'done')
                                <form method="POST"
                                    action="{{ route('meeting.cancel', $meeting) }}"
                                    class="sweet-confirm"
                                    data-message="Yakin ingin membatalkan pertemuan ini? Pertemuan yang dibatalkan tidak dapat diaktifkan kembali.">
                                    @csrf

                                    <button
                                        class="px-4 py-2 rounded-lg text-sm font-medium
                                            bg-yellow-500 text-white
                                            hover:bg-yellow-600 transition">
                                        Batalkan
                                    </button>
                                </form>
                            @endif

                            {{-- DELETE MEETING --}}
                            <form method="POST"
                                action="{{ route('meeting.delete', $meeting) }}"
                                class="sweet-confirm"
                                data-message="Yakin ingin menghapus pertemuan ini? Data tidak akan bisa dikembalikan.">
                                @csrf
                                @method('DELETE')

                                <button
                                    class="px-4 py-2 rounded-lg text-sm font-medium
                                        bg-red-600 text-white
                                        hover:bg-red-700 transition">
                                    Hapus
                                </button>
                            </form>

                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-12
                        text-gray-500 dark:text-gray-400">
                Belum ada pertemuan untuk course ini.
            </div>
        @endforelse

    </div></a>

</div>
@endsection

@push('scripts')
<script>
    const searchInput = document.getElementById('meeting-search');
    const cards = document.querySelectorAll('.meeting-card');

    searchInput?.addEventListener('input', function () {
        const keyword = this.value.toLowerCase();

        cards.forEach(card => {
            const title = card.querySelector('.meeting-title')
                              .innerText.toLowerCase();

            card.style.display = title.includes(keyword)
                ? 'block'
                : 'none';
        });
    });
</script>
@endpush
