@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 p-4">

    {{-- ================= HEADER ================= --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-2xl font-bold text-azwara-darker dark:text-white">
                Laporan Pemasukan
            </h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                Ringkasan pemasukan yang sudah diverifikasi
            </p>
        </div>

        <div class="flex items-center gap-3">
            <span class="px-4 py-2 bg-green-50 dark:bg-green-900/30
                        text-green-600 dark:text-green-300
                        rounded-lg font-medium">
                {{ Carbon\Carbon::parse($selectedMonth)->translatedFormat('F Y') }}
            </span>

            @if($payments->isNotEmpty())
                <a href="{{ route('reports.income.export', ['month' => $selectedMonth]) }}"
                class="px-4 py-2 bg-red-600 hover:bg-red-700
                        text-white text-sm font-medium rounded-lg transition">
                    📄 Export PDF
                </a>
            @endif
        </div>

    </div>
    {{-- ================= STATS CARDS ================= --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-xl p-5 border border-green-200 dark:border-green-700/30">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-green-600 dark:text-green-400 font-medium">Total Pemasukan</p>
                    <h3 class="text-2xl font-bold text-gray-800 dark:text-white mt-1">
                        Rp {{ number_format($totalIncome, 0, ',', '.') }}
                    </h3>
                </div>
                <div class="p-3 bg-green-100 dark:bg-green-800/30 rounded-lg">
                    <span class="text-xl">💰</span>
                </div>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                {{ $startDate->translatedFormat('d M') }} - {{ $endDate->translatedFormat('d M Y') }}
            </p>
        </div>

        <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-xl p-5 border border-blue-200 dark:border-blue-700/30">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-blue-600 dark:text-blue-400 font-medium">Manual QRIS</p>
                    <h3 class="text-2xl font-bold text-gray-800 dark:text-white mt-1">
                        Rp {{ number_format($paymentMethods['manual_qris']['total'] ?? 0, 0, ',', '.') }}
                    </h3>
                </div>
                <div class="p-3 bg-blue-100 dark:bg-blue-800/30 rounded-lg">
                    <span class="text-xl">🏦</span>
                </div>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                {{ $paymentMethods['manual_qris']['count'] ?? 0 }} transaksi
            </p>
        </div>

        <div class="bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 rounded-xl p-5 border border-purple-200 dark:border-purple-700/30">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-purple-600 dark:text-purple-400 font-medium">Midtrans</p>
                    <h3 class="text-2xl font-bold text-gray-800 dark:text-white mt-1">
                        Rp {{ number_format($paymentMethods['midtrans']['total'] ?? 0, 0, ',', '.') }}
                    </h3>
                </div>
                <div class="p-3 bg-purple-100 dark:bg-purple-800/30 rounded-lg">
                    <span class="text-xl">💳</span>
                </div>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                {{ $paymentMethods['midtrans']['count'] ?? 0 }} transaksi
            </p>
        </div>

        <div class="bg-gradient-to-br from-orange-50 to-orange-100 dark:from-orange-900/20 dark:to-orange-800/20 rounded-xl p-5 border border-orange-200 dark:border-orange-700/30">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-orange-600 dark:text-orange-400 font-medium">Total Transaksi</p>
                    <h3 class="text-2xl font-bold text-gray-800 dark:text-white mt-1">
                        {{ $payments->count() }}
                    </h3>
                </div>
                <div class="p-3 bg-orange-100 dark:bg-orange-800/30 rounded-lg">
                    <span class="text-xl">📊</span>
                </div>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                Semua pembayaran verified
            </p>
        </div>
    </div>

    {{-- ================= FILTER FORM ================= --}}
    <div class="bg-azwara-lightest dark:bg-gray-800 rounded-2xl p-6 shadow-lg border border-gray-200 dark:border-gray-700">
        <form method="GET" action="{{ route('reports.income') }}" class="flex flex-col sm:flex-row gap-4 items-end">
            <div class="flex-1">
                <label for="month" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Pilih Bulan
                </label>
                <select name="month" id="month"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                    @foreach($months as $value => $label)
                        <option value="{{ $value }}" {{ $selectedMonth == $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit"
                        class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition">
                    Filter
                </button>
                <a href="{{ route('reports.income') }}"
                   class="px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- ================= PAYMENTS TABLE ================= --}}
    <div class="bg-azwara-lightest dark:bg-gray-800 rounded-2xl p-6 shadow-lg border border-gray-200 dark:border-gray-700">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 gap-4">
            <div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
                    Detail Transaksi
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ $payments->count() }} transaksi •
                    Rp {{ number_format($totalIncome, 0, ',', '.') }} total
                </p>
            </div>
            <div class="flex items-center gap-2">
                @if($payments->isNotEmpty())
                    <a href="{{ route('reports.income.export', ['month' => $selectedMonth]) }}"
                       class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition">
                        📥 Export Excel
                    </a>
                @endif
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">No</th>
                        <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tanggal</th>
                        <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Customer</th>
                        <th class="text-left py-3 px-3 px-4 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Metode</th>
                        <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">No Order</th>
                        <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Jumlah</th>
                        <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $index => $payment)
                        @php
                            $order = $payment->order;
                            $methodColors = [
                                'manual_qris' => 'bg-blue-100 text-blue-800 dark:bg-blue-500/20 dark:text-blue-300',
                                'midtrans' => 'bg-purple-100 text-purple-800 dark:bg-purple-500/20 dark:text-purple-300',
                            ];
                            $methodLabels = [
                                'manual_qris' => 'QRIS',
                                'midtrans' => 'Midtrans',
                            ];
                        @endphp
                        <tr class="border-b border-gray-100 dark:border-gray-700/50 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                            <td class="py-3 px-4 text-sm text-gray-500 dark:text-gray-400">
                                {{ $index + 1 }}
                            </td>
                            <td class="py-3 px-4">
                                <div class="text-sm text-gray-800 dark:text-white">
                                    {{ $payment->verified_at->translatedFormat('d M Y') }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $payment->verified_at->format('H:i') }}
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-medium text-gray-800 dark:text-white">
                                    {{ $order->user->name }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $order->user->email }}
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-3 py-1 text-xs font-medium rounded-full {{ $methodColors[$payment->method] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $methodLabels[$payment->method] ?? $payment->method }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <div class="text-sm text-gray-800 dark:text-white font-mono">
                                    #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-semibold text-green-600 dark:text-green-400">
                                    Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-3 py-1 bg-green-100 dark:bg-green-500/20 text-green-800 dark:text-green-300 text-xs font-medium rounded-full">
                                    VERIFIED
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center justify-center">
                                    <span class="text-4xl mb-4">💰</span>
                                    <h3 class="text-lg font-semibold mb-2">Tidak Ada Transaksi</h3>
                                    <p class="max-w-md">
                                        Tidak ada pembayaran yang sudah diverifikasi pada
                                        {{ Carbon\Carbon::parse($selectedMonth)->translatedFormat('F Y') }}
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- DAILY SUMMARY --}}
        @if($payments->isNotEmpty())
            @php
                $dailySummary = $payments->groupBy(function($payment) {
                    return $payment->verified_at->format('Y-m-d');
                })->map(function($group) {
                    return [
                        'date' => $group->first()->verified_at,
                        'count' => $group->count(),
                        'total' => $group->sum(function($payment) {
                            return $payment->order->total_amount;
                        })
                    ];
                })->sortBy('date');
            @endphp

            <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">Ringkasan Harian</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach($dailySummary as $day)
                        <div class="bg-gray-50 dark:bg-gray-900/30 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $day['date']->translatedFormat('d M') }}
                                    </p>
                                    <p class="text-lg font-bold text-gray-800 dark:text-white mt-1">
                                        Rp {{ number_format($day['total'], 0, ',', '.') }}
                                    </p>
                                </div>
                                <div class="p-2 bg-gray-100 dark:bg-gray-800 rounded-lg">
                                    <span class="text-sm">{{ $day['count'] }}x</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

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
