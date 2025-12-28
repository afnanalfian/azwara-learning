@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-azwara-darker dark:text-azwara-lightest">
                Order Pembelian
            </h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Daftar transaksi siswa yang masuk
            </p>
        </div>

        {{-- SEARCH --}}
        <form method="GET"
            class="w-full sm:w-80 flex items-center gap-2">

            <input
                type="text"
                name="q"
                value="{{ request('q') }}"
                placeholder="Cari nama siswa..."
                class="flex-1 rounded-xl border border-gray-300 dark:border-azwara-darker
                    bg-azwara-lightest dark:bg-azwara-darkest
                    px-4 py-2.5 text-sm
                    text-gray-900 dark:text-white
                    focus:ring focus:ring-primary/30 focus:outline-none"
            >

            <button
                type="submit"
                class="shrink-0 rounded-xl px-4 py-2.5
                    bg-primary hover:bg-primary/90
                    text-white text-sm font-semibold
                    transition">
                Cari
            </button>

        </form>

    </div>

    {{-- DESKTOP TABLE --}}
    <div class="hidden md:block overflow-hidden rounded-2xl border dark:border-azwara-darker
                bg-azwara-lightest dark:bg-azwara-darkest">

        <table class="min-w-full text-sm">
            <thead class="bg-primary dark:bg-azwara-darker">
                <tr class="text-left text-white dark:text-gray-300">
                    <th class="px-6 py-4">Order</th>
                    <th class="px-6 py-4">Siswa</th>
                    <th class="px-6 py-4">Total</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4">Waktu</th>
                    <th class="px-6 py-4"></th>
                </tr>
            </thead>

            <tbody class="divide-y dark:divide-azwara-darker">
                @forelse($orders as $order)
                    <tr class="hover:bg-azwara-lighter dark:hover:bg-azwara-darker/50">
                        <td class="px-6 py-4 font-semibold dark:text-azwara-lightest">
                            #{{ $order->id }}
                        </td>

                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900 dark:text-azwara-lightest">
                                {{ $order->user->name }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $order->user->email }}
                            </div>
                        </td>

                        <td class="px-6 py-4 font-semibold dark:text-azwara-lightest">
                            Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                        </td>

                        <td class="px-6 py-4">
                            @php
                                $statusColor = match($order->status) {
                                    'pending'  => 'bg-yellow-100 text-yellow-700',
                                    'verified' => 'bg-green-100 text-green-700',
                                    'rejected' => 'bg-red-100 text-red-700',
                                    default    => 'bg-gray-100 text-gray-700',
                                };
                            @endphp

                            <span class="inline-flex px-3 py-1 rounded-lg text-xs font-semibold {{ $statusColor }}">
                                {{ strtoupper($order->status) }}
                            </span>
                        </td>

                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                            {{ $order->created_at->format('d M Y H:i') }}
                        </td>

                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('orders.show', $order) }}"
                               class="text-primary font-semibold hover:underline dark:text-azwara-lightest">
                                Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                            Belum ada order masuk.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- MOBILE CARD LIST --}}
    <div class="md:hidden space-y-4">
        @forelse($orders as $order)
            @php
                $statusColor = match($order->status) {
                    'pending'  => 'bg-yellow-100 text-yellow-700',
                    'verified' => 'bg-green-100 text-green-700',
                    'rejected' => 'bg-red-100 text-red-700',
                    default    => 'bg-gray-100 text-gray-700',
                };
            @endphp

            <div class="rounded-2xl border border-gray-200 dark:border-azwara-darker
                        bg-azwara-lightest dark:bg-azwara-darkest p-4 space-y-3">

                <div class="flex items-center justify-between">
                    <div class="font-semibold text-gray-900 dark:text-azwara-lightest">
                        #{{ $order->id }}
                    </div>

                    <span class="px-3 py-1 rounded-lg text-xs font-semibold {{ $statusColor }}">
                        {{ strtoupper($order->status) }}
                    </span>
                </div>

                <div>
                    <div class="font-medium text-gray-900 dark:text-azwara-lightest">
                        {{ $order->user->name }}
                    </div>
                    <div class="text-xs text-gray-500">
                        {{ $order->user->email }}
                    </div>
                </div>

                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Total</span>
                    <span class="font-semibold dark:text-azwara-lightest">
                        Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                    </span>
                </div>

                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Waktu</span>
                    <span class="dark:text-azwara-lightest">
                        {{ $order->created_at->format('d M Y H:i') }}
                    </span>
                </div>

                <a href="{{ route('orders.show', $order) }}"
                   class="block text-center rounded-xl
                          bg-primary text-white font-semibold py-2">
                    Lihat Detail
                </a>
            </div>
        @empty
            <div class="text-center text-gray-500 py-10">
                Belum ada order masuk.
            </div>
        @endforelse
    </div>

    {{-- PAGINATION --}}
    <div>
        {{ $orders->links() }}
    </div>

</div>
@endsection
