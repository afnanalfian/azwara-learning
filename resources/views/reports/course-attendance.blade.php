@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 p-4">

    {{-- ================= HEADER ================= --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-azwara-darker dark:text-azwara-lighter">
                Course Attendance Report
            </h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                Persentase kehadiran siswa per course
            </p>
        </div>
    </div>

    {{-- ================= FILTER FORM ================= --}}
    <div class="bg-azwara-lightest dark:bg-gray-800 rounded-2xl p-6 shadow-lg border border-gray-200 dark:border-gray-700">
        <form method="GET" action="{{ route('reports.course-attendance') }}" class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <label for="course_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Pilih Course
                </label>
                <select name="course_id" id="course_id"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                    <option value="">-- Pilih Course --</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}"
                                {{ $selectedCourseId == $course->id ? 'selected' : '' }}>
                            {{ $course->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit"
                        class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition">
                    Filter
                </button>
            </div>
        </form>
    </div>

    {{-- ================= ATTENDANCE TABLE ================= --}}
    @if($selectedCourseId)
        <div class="bg-azwara-lightest dark:bg-gray-800 rounded-2xl p-6 shadow-lg border border-gray-200 dark:border-gray-700">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 gap-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
                        {{ $courses->find($selectedCourseId)->name ?? 'Course' }}
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ $attendanceData->count() }} siswa •
                        {{ $attendanceData->first()['total_done_meetings'] ?? 0 }} meeting done
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1.5 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-sm rounded-lg">
                        Diurutkan dari yang paling sering hadir
                    </span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">No</th>
                            <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Siswa</th>
                            <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Email</th>
                            <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kehadiran</th>
                            <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Persentase</th>
                            <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Progress</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendanceData as $index => $data)
                            @php
                                $student = $data['student'];
                                $attended = $data['attended_count'];
                                $total = $data['total_done_meetings'];
                                $percentage = $data['percentage'];

                                // Warna berdasarkan persentase
                                $colorClass = match(true) {
                                    $percentage >= 80 => 'text-green-600 dark:text-green-400',
                                    $percentage >= 60 => 'text-blue-600 dark:text-blue-400',
                                    $percentage >= 40 => 'text-yellow-600 dark:text-yellow-400',
                                    $percentage !== null => 'text-red-600 dark:text-red-400',
                                    default => 'text-gray-500 dark:text-gray-400'
                                };

                                $bgClass = match(true) {
                                    $percentage >= 80 => 'bg-green-500',
                                    $percentage >= 60 => 'bg-blue-500',
                                    $percentage >= 40 => 'bg-yellow-500',
                                    $percentage !== null => 'bg-red-500',
                                    default => 'bg-gray-300'
                                };
                            @endphp
                            <tr class="border-b border-gray-100 dark:border-gray-700/50 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                                <td class="py-3 px-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $index + 1 }}
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-medium text-gray-800 dark:text-white">
                                        {{ $student->name }}
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ $student->email }}
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="text-sm font-medium {{ $colorClass }}">
                                        {{ $attended }}/{{ $total }} meeting
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="text-sm font-semibold {{ $colorClass }}">
                                        @if($percentage !== null)
                                            {{ $percentage }}%
                                        @else
                                            -
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                                        @if($percentage !== null)
                                            <div class="h-2.5 rounded-full {{ $bgClass }}"
                                                 style="width: {{ min($percentage, 100) }}%">
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-gray-500 dark:text-gray-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <span class="text-3xl mb-3">📊</span>
                                        <p>Tidak ada data kehadiran untuk course ini</p>
                                        <p class="text-sm mt-1">Pastikan sudah ada meeting yang selesai (done)</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- SUMMARY STATS --}}
            @if($attendanceData->isNotEmpty() && $total > 0)
                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Statistik Kehadiran</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                        <div class="bg-gray-50 dark:bg-gray-900/30 rounded-lg p-4">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Rata-rata Kehadiran</p>
                            <p class="text-xl font-bold text-gray-800 dark:text-white mt-1">
                                {{ round($attendanceData->avg('percentage'), 1) }}%
                            </p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-900/30 rounded-lg p-4">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Siswa Terhadir</p>
                            <p class="text-xl font-bold text-gray-800 dark:text-white mt-1">
                                {{ $attendanceData->where('percentage', 100)->count() }}
                            </p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-900/30 rounded-lg p-4">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Siswa di atas 80%</p>
                            <p class="text-xl font-bold text-gray-800 dark:text-white mt-1">
                                {{ $attendanceData->where('percentage', '>=', 80)->count() }}
                            </p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-900/30 rounded-lg p-4">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Siswa di bawah 50%</p>
                            <p class="text-xl font-bold text-gray-800 dark:text-white mt-1">
                                {{ $attendanceData->where('percentage', '<', 50)->count() }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @else
        <div class="bg-azwara-lightest dark:bg-gray-800 rounded-2xl p-8 shadow-lg border border-gray-200 dark:border-gray-700 text-center">
            <div class="flex flex-col items-center justify-center">
                <span class="text-4xl mb-4">📊</span>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">
                    Pilih Course untuk Melihat Laporan
                </h3>
                <p class="text-gray-600 dark:text-gray-400 max-w-md">
                    Pilih course dari dropdown di atas untuk melihat persentase kehadiran siswa pada course tersebut.
                </p>
            </div>
        </div>
    @endif

</div>
@endsection

@push('styles')
<style>
    tr {
        transition: background-color 0.2s ease;
    }

    .overflow-x-auto::-webkit-scrollbar {
        height: 6px;
    }

    .overflow-x-auto::-webkit-scrollbar-track {
        @apply bg-gray-100 dark:bg-gray-700;
    }

    .overflow-x-auto::-webkit-scrollbar-thumb {
        @apply bg-gray-300 dark:bg-gray-600 rounded-full;
    }
</style>
@endpush
