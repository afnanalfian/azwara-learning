@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 px-4 sm:px-6 lg:px-0">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-azwara-darker dark:text-white">
                Discount & Voucher
            </h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Kelola kode promo dan potongan harga
            </p>
        </div>

        <a href="{{ route('discounts.create') }}"
           class="inline-flex items-center justify-center
                  bg-primary hover:bg-azwara-medium
                  text-white font-semibold
                  px-5 py-2.5 rounded-xl transition">
            + Tambah Discount
        </a>
    </div>

    {{-- MOBILE LIST --}}
    <div class="space-y-4 sm:hidden">
        @forelse($discounts as $discount)
            <div class="rounded-2xl border dark:border-azwara-darker
                        bg-azwara-lightest dark:bg-azwara-darkest
                        p-4 space-y-3">

                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white">
                            {{ $discount->name }}
                        </h3>
                        <p class="mt-1 font-mono text-xs font-semibold
                            inline-block px-2 py-1 rounded-lg
                            bg-gray-100 dark:bg-azwara-darker
                            text-gray-700 dark:text-gray-300">
                            {{ $discount->code }}
                        </p>
                    </div>

                    <span class="inline-flex px-3 py-1 rounded-lg text-xs font-semibold
                        {{ $discount->is_currently_active
                            ? 'bg-green-100 text-green-700'
                            : 'bg-gray-100 text-gray-600' }}">
                        {{ $discount->is_currently_active ? 'AKTIF' : 'NONAKTIF' }}
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-3 text-sm text-gray-700 dark:text-gray-300">
                    <div>
                        <p class="text-xs text-gray-500">Tipe</p>
                        <p class="font-medium">{{ $discount->type_label }}</p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500">Nilai</p>
                        <p class="font-semibold text-gray-900 dark:text-white">
                            @if($discount->type === 'percentage')
                                {{ $discount->value }}%
                            @else
                                Rp {{ number_format($discount->value, 0, ',', '.') }}
                            @endif
                        </p>
                    </div>

                    <div class="col-span-2">
                        <p class="text-xs text-gray-500">Berlaku</p>
                        <p>
                            {{ $discount->starts_at?->format('d M Y') ?? '—' }}
                            –
                            {{ $discount->ends_at?->format('d M Y') ?? '—' }}
                        </p>
                    </div>
                </div>

                <div class="flex justify-end pt-2">
                    <a href="{{ route('discounts.show', $discount) }}"
                       class="text-primary font-semibold hover:underline dark:text-azwara-lighter">
                        Detail
                    </a>
                </div>
            </div>
        @empty
            <div class="text-center text-gray-500 py-10">
                Belum ada discount.
            </div>
        @endforelse
    </div>

    {{-- DESKTOP TABLE --}}
    <div class="hidden sm:block overflow-hidden rounded-2xl border
                dark:border-azwara-darker
                bg-azwara-lightest dark:bg-azwara-darkest">

        <table class="min-w-full text-sm">
            <thead class="bg-primary dark:bg-azwara-darker">
                <tr class="text-left text-azwara-lightest dark:text-gray-300">
                    <th class="px-6 py-4">Nama</th>
                    <th class="px-6 py-4">Kode</th>
                    <th class="px-6 py-4">Tipe</th>
                    <th class="px-6 py-4">Nilai</th>
                    <th class="px-6 py-4">Berlaku</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-right"></th>
                </tr>
            </thead>

            <tbody class="divide-y dark:divide-azwara-darker">
                @forelse($discounts as $discount)
                    <tr class="hover:bg-azwara-lighter dark:hover:bg-azwara-darker/50">
                        <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white">
                            {{ $discount->name }}
                        </td>

                        <td class="px-6 py-4 font-mono font-semibold text-gray-900 dark:text-white">
                            {{ $discount->code }}
                        </td>

                        <td class="px-6 py-4 dark:text-white">
                            {{ $discount->type_label }}
                        </td>

                        <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white">
                            @if($discount->type === 'percentage')
                                {{ $discount->value }}%
                            @else
                                Rp {{ number_format($discount->value, 0, ',', '.') }}
                            @endif
                        </td>

                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                            {{ $discount->starts_at?->format('d M Y') ?? '—' }}
                            –
                            {{ $discount->ends_at?->format('d M Y') ?? '—' }}
                        </td>

                        <td class="px-6 py-4">
                            <span class="inline-flex px-3 py-1 rounded-lg text-xs font-semibold
                                {{ $discount->is_currently_active
                                    ? 'bg-green-100 text-green-700'
                                    : 'bg-gray-100 text-gray-600' }}">
                                {{ $discount->is_currently_active ? 'AKTIF' : 'NONAKTIF' }}
                            </span>
                        </td>

                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('discounts.show', $discount) }}"
                               class="text-primary font-semibold hover:underline dark:text-azwara-lighter">
                                Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-gray-500">
                            Belum ada discount.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pt-4">
        {{ $discounts->links() }}
    </div>

</div>
@endsection
