@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 p-4">

    {{-- ================= HEADER ================= --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-white">
                Admin Dashboard
            </h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                Overview & weekly activity monitoring
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="?week={{ $weekOffset - 1 }}"
               class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:text-azwara-lightest
                      hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                ← Prev
            </a>
            <span class="px-4 py-2 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300
                        rounded-lg font-medium">
                {{ $weekLabel }}
            </span>
            <a href="?week={{ $weekOffset + 1 }}"
               class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:text-azwara-lightest
                      hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                Next →
            </a>
        </div>
    </div>

    {{-- ================= STATS CARDS ================= --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20
                    rounded-xl p-5 border border-blue-200 dark:border-blue-700/30">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-blue-600 dark:text-blue-400 font-medium">Total Siswa</p>
                    <h3 class="text-2xl font-bold text-gray-800 dark:text-white mt-1">
                        {{ $stats['total_siswa'] }}
                    </h3>
                </div>
                <div class="p-3 bg-blue-100 dark:bg-blue-800/30 rounded-lg">
                    <span class="text-xl">👨‍🎓</span>
                </div>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Active students</p>
        </div>

        <div class="bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20
                    rounded-xl p-5 border border-purple-200 dark:border-purple-700/30">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-purple-600 dark:text-purple-400 font-medium">Total Tentor</p>
                    <h3 class="text-2xl font-bold text-gray-800 dark:text-white mt-1">
                        {{ $stats['total_tentor'] }}
                    </h3>
                </div>
                <div class="p-3 bg-purple-100 dark:bg-purple-800/30 rounded-lg">
                    <span class="text-xl">👨‍🏫</span>
                </div>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Active tutors</p>
        </div>

        <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20
                    rounded-xl p-5 border border-green-200 dark:border-green-700/30">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-green-600 dark:text-green-400 font-medium">Total Course</p>
                    <h3 class="text-2xl font-bold text-gray-800 dark:text-white mt-1">
                        {{ $stats['total_course'] }}
                    </h3>
                </div>
                <div class="p-3 bg-green-100 dark:bg-green-800/30 rounded-lg">
                    <span class="text-xl">📚</span>
                </div>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Available courses</p>
        </div>

        <div class="bg-gradient-to-br from-orange-50 to-orange-100 dark:from-orange-900/20 dark:to-orange-800/20
                    rounded-xl p-5 border border-orange-200 dark:border-orange-700/30">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-orange-600 dark:text-orange-400 font-medium">Meetings</p>
                    <h3 class="text-2xl font-bold text-gray-800 dark:text-white mt-1">
                        {{ $stats['done_meeting'] }}<span class="text-lg">/{{ $stats['total_meeting'] }}</span>
                    </h3>
                </div>
                <div class="p-3 bg-orange-100 dark:bg-orange-800/30 rounded-lg">
                    <span class="text-xl">🎯</span>
                </div>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Completed/Total</p>
        </div>
    </div>

    {{-- ================= WEEKLY SALES ================= --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg border border-gray-200 dark:border-gray-700">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 gap-4">
            <div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Weekly Sales</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Revenue overview for {{ $weekLabel }}</p>
            </div>
            <div class="flex items-center gap-2">
                <div class="flex items-center gap-2 px-3 py-1.5 bg-blue-50 dark:bg-blue-900/30 rounded-lg">
                    <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                    <span class="text-sm text-blue-600 dark:text-blue-400">Total Revenue</span>
                </div>
            </div>
        </div>

        <div class="h-72">
            <canvas id="weeklySalesChart"></canvas>
        </div>
    </div>

    {{-- ================= TABLES ================= --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- MEETINGS THIS WEEK --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Meetings This Week</h3>
                <span class="text-sm text-gray-500">{{ $meetingsThisWeek->count() }} meetings</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Meeting</th>
                            <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Course</th>
                            <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($meetingsThisWeek as $meeting)
                            <tr class="border-b border-gray-100 dark:border-gray-700/50 hover:bg-gray-50 dark:hover:bg-gray-700/30 cursor-pointer transition"
                                onclick="window.location='{{ route('meeting.show', $meeting) }}'">
                                <td class="py-3 px-4">
                                    <div class="font-medium text-gray-800 dark:text-white">{{ $meeting->title }}</div>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="text-sm text-gray-600 dark:text-gray-400">{{ $meeting->course->name ?? '-' }}</div>
                                </td>
                                <td class="py-3 px-4">
                                    @php
                                        $statusColors = [
                                            'live' => 'bg-green-100 text-green-800 dark:bg-green-500/20 dark:text-green-300',
                                            'upcoming' => 'bg-blue-100 text-blue-800 dark:bg-blue-500/20 dark:text-blue-300',
                                            'done' => 'bg-gray-100 text-gray-800 dark:bg-gray-500/20 dark:text-gray-300',
                                            'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-500/20 dark:text-red-300'
                                        ];
                                        $color = $statusColors[$meeting->status] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="px-3 py-1 text-xs font-medium rounded-full {{ $color }}">
                                        {{ ucfirst($meeting->status) }}
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ $meeting->scheduled_at->format('d M H:i') }}
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-gray-500 dark:text-gray-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <span class="text-2xl mb-2">📅</span>
                                        No meetings scheduled this week
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- PENDING ORDERS --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Orders Waiting Confirmation</h3>
                <span class="px-3 py-1 bg-yellow-100 dark:bg-yellow-500/20 text-yellow-800 dark:text-yellow-300 text-sm font-medium rounded-full">
                    {{ $pendingOrders->count() }} pending
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">User</th>
                            <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total</th>
                            <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingOrders as $order)
                            <tr class="border-b border-gray-100 dark:border-gray-700/50 hover:bg-gray-50 dark:hover:bg-gray-700/30 cursor-pointer transition"
                                onclick="window.location='{{ route('orders.show', $order) }}'">
                                <td class="py-3 px-4">
                                    <div class="font-medium text-gray-800 dark:text-white">{{ $order->user->name }}</div>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-semibold text-gray-800 dark:text-white">
                                        Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="px-3 py-1 bg-yellow-100 dark:bg-yellow-500/20 text-yellow-800 dark:text-yellow-300 text-xs font-medium rounded-full">
                                        PAID
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ $order->created_at->format('d M Y') }}
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-gray-500 dark:text-gray-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <span class="text-2xl mb-2">✅</span>
                                        All orders are verified
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>
@endsection

@push('styles')
<style>
    /* Smooth hover transitions */
    tr {
        transition: background-color 0.2s ease;
    }

    /* Better scrollbar for tables */
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('weeklySalesChart');

        // Data dari controller
        const labels = @json(array_column($allDays, 'day'));
        const dates = @json(array_column($allDays, 'date'));
        const data = @json(array_column($allDays, 'total'));

        // Format untuk tooltip
        const formattedData = data.map(value => {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(value);
        });

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Revenue',
                    data: data,
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderColor: '#3b82f6',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#3b82f6',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: function(context) {
                                return `Revenue: ${formattedData[context.dataIndex]}`;
                            },
                            title: function(context) {
                                return dates[context[0].dataIndex];
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });
    });
</script>
@endpush
