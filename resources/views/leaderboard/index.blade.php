@extends('layouts.app')

@section('content')

<div class="max-w-7xl mx-auto space-y-6">

    {{-- ================= HEADER ================= --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-azwara-darker dark:text-white">
                🏆 Leaderboard Siswa
            </h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Peringkat global berdasarkan seluruh evaluasi (Post Test, Quiz, Tryout)
            </p>
        </div>
                                    {{-- DETAIL --}}
                            <div class="px-4 py-3 text-right">
                                <a href="{{ route('leaderboard.detail')}}"
                                   class="inline-flex items-center gap-1
                                          text-sm font-medium
                                          text-primary hover:underline dark:text-azwara-lighter">
                                    Lihat Detail →
                                </a>
                            </div>
    </div>

    {{-- ================= TABLE CARD ================= --}}
    <div class="bg-azwara-lightest dark:bg-secondary/80
                backdrop-blur rounded-2xl
                border border-azwara-light/30 dark:border-white/10
                shadow-sm overflow-hidden">

        <table class="w-full text-md">
            {{-- ===== HEADER DESKTOP ===== --}}
            <thead class="hidden md:table-header-group
                        bg-azwara-darker dark:bg-white/5
                        text-azwara-lightest dark:text-gray-300">
                <tr>
                    <th class="px-4 py-3 text-left w-20">Rank</th>
                    <th class="px-4 py-3 text-left">Nama</th>
                    <th class="px-4 py-3 text-center w-28">Post Test</th>
                    <th class="px-4 py-3 text-center w-28">Quiz</th>
                    <th class="px-4 py-3 text-center w-28">Tryout</th>
                    <th class="px-4 py-3 text-center w-32">Akumulasi</th>
                </tr>
            </thead>

            {{-- ===== BODY ===== --}}
            <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                @forelse($leaderboard as $i => $row)
                    @php $rank = $i + 1; @endphp

                    <tr class="block md:table-row
                            px-4 md:px-0 py-4 md:py-0
                            hover:bg-azwara-lightest/60
                            dark:hover:bg-white/5 transition">

                        {{-- RANK DESKTOP --}}
                        <td class="hidden md:table-cell px-4 py-3 font-semibold dark:text-white">
                            @if($rank === 1)
                                🥇
                            @elseif($rank === 2)
                                🥈
                            @elseif($rank === 3)
                                🥉
                            @else
                                #{{ $rank }}
                            @endif
                        </td>

                        {{-- NAMA + INFO MOBILE --}}
                        <td class="block md:table-cell py-2 md:py-3 space-y-2">

                            {{-- Nama --}}
                            <div class="flex items-center justify-between md:block">
                                <span class="font-medium text-gray-900 dark:text-white">
                                    {{ $row->name }}
                                </span>

                                {{-- Rank MOBILE --}}
                                <span class="md:hidden text-xs font-semibold dark:text-azwara-lightest">
                                    @if($rank === 1)
                                        🥇 #1
                                    @elseif($rank === 2)
                                        🥈 #2
                                    @elseif($rank === 3)
                                        🥉 #3
                                    @else
                                        #{{ $rank }}
                                    @endif
                                </span>
                            </div>

                            {{-- INFO MOBILE --}}
                            <div class="grid grid-cols-2 gap-x-4 gap-y-1
                                        text-xs text-gray-600 dark:text-gray-400
                                        md:hidden">

                                <div>Post Test:
                                    <strong class="text-primary dark:text-azwara-lighter">
                                        {{ $row->post_test_avg !== null
                                            ? number_format($row->post_test_avg, 2)
                                            : '–' }}
                                    </strong>
                                </div>

                                <div>Quiz:
                                    <strong class="text-primary dark:text-azwara-lighter">
                                        {{ $row->quiz_avg !== null
                                            ? number_format($row->quiz_avg, 2)
                                            : '–' }}
                                    </strong>
                                </div>

                                <div>Tryout:
                                    <strong class="text-primary dark:text-azwara-lighter">
                                        {{ $row->tryout_avg !== null
                                            ? number_format($row->tryout_avg, 2)
                                            : '–' }}
                                    </strong>
                                </div>

                                <div>Total:
                                    <strong class="text-primary dark:text-azwara-lighter">
                                        {{ $row->total_avg > 0
                                            ? number_format($row->total_avg, 2)
                                            : '–' }}
                                    </strong>
                                </div>
                            </div>
                        </td>

                        {{-- POST TEST DESKTOP --}}
                        <td class="hidden md:table-cell px-4 py-3 text-center dark:text-azwara-lightest">
                            {{ $row->post_test_avg !== null
                                ? number_format($row->post_test_avg, 2)
                                : '–' }}
                        </td>

                        {{-- QUIZ DESKTOP --}}
                        <td class="hidden md:table-cell px-4 py-3 text-center dark:text-azwara-lightest">
                            {{ $row->quiz_avg !== null
                                ? number_format($row->quiz_avg, 2)
                                : '–' }}
                        </td>

                        {{-- TRYOUT DESKTOP --}}
                        <td class="hidden md:table-cell px-4 py-3 text-center dark:text-azwara-lightest">
                            {{ $row->tryout_avg !== null
                                ? number_format($row->tryout_avg, 2)
                                : '–' }}
                        </td>

                        {{-- TOTAL DESKTOP --}}
                        <td class="hidden md:table-cell px-4 py-3
                                text-center font-semibold text-primary dark:text-azwara-lightest">
                            {{ $row->total_avg > 0
                                ? number_format($row->total_avg, 2)
                                : '–' }}
                        </td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="6"
                            class="px-4 py-10 text-center text-gray-500">
                            Belum ada data leaderboard
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

@endsection
