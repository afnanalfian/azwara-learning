<div class="rounded-xl p-6
            bg-azwara-lightest dark:bg-secondary/70
            border border-azwara-light/30 dark:border-white/10">

    <div class="flex flex-col lg:flex-row gap-6 lg:items-center lg:justify-between">

        {{-- LEFT --}}
        <div class="min-w-0">
            <h1 class="text-2xl font-bold
                       text-azwara-darker dark:text-azwara-lighter">
                {{ $meeting->title }}
            </h1>

            <div class="mt-2 text-sm
                        text-gray-600 dark:text-gray-300">
                {{ $meeting->scheduled_at->translatedFormat('l, d F Y • H:i') }} WIB
            </div>

            <span class="inline-block mt-3
                         px-3 py-1 rounded-full text-xs font-semibold
                         @if($meeting->status === 'live')
                            bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-300
                         @elseif($meeting->status === 'upcoming')
                            bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300
                         @else
                            bg-gray-200 text-gray-700 dark:bg-gray-500/20 dark:text-gray-300
                         @endif">
                {{ ucfirst($meeting->status) }}
            </span>
        </div>

        {{-- ACTIONS (ADMIN ONLY) --}}
        <div class="flex flex-col sm:flex-row gap-2 w-full lg:w-auto">
            @role('admin')
                <a href="{{ route('meeting.edit', $meeting) }}"
                class="w-full sm:w-auto text-center
                        px-4 py-2 rounded-lg text-sm font-medium
                        bg-primary text-white hover:bg-primary/90 transition">
                    Edit
                </a>
            @endrole

            @if($meeting->status !== 'done')
                @if($meeting->zoom_link)
                    <a href="{{ route('meeting.joinZoom', $meeting) }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="w-full sm:w-auto text-center
                            px-4 py-2 rounded-lg text-sm font-medium
                            bg-green-600 text-white hover:bg-green-700 transition">
                        Join Zoom
                    </a>
                @else
                    <button disabled
                            class="w-full sm:w-auto
                                px-4 py-2 rounded-lg text-sm font-medium
                                bg-gray-400 text-white cursor-not-allowed">
                        Belum Ada Link Zoom
                    </button>
                @endif
            @endif

            @role('admin')
            <form method="POST"
                  action="{{ route('meeting.destroy', $meeting) }}"
                  class="sweet-confirm w-full sm:w-auto"
                  data-message="Yakin ingin menghapus pertemuan ini?">
                @csrf
                @method('DELETE')

                <button
                    class="w-full sm:w-auto
                           px-4 py-2 rounded-lg text-sm font-medium
                           bg-red-600 text-white hover:bg-red-700 transition">
                    Hapus
                </button>
            </form>
            @endrole

        </div>

    </div>
</div>
