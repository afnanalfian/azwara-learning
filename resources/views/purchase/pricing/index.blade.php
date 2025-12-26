@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 px-4 sm:px-6 lg:px-0">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-azwara-lightest">
                Pricing Rules
            </h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Atur harga berdasarkan jumlah, tipe produk, dan status aktif
            </p>
        </div>

        <a href="{{ route('pricing.create') }}"
           class="inline-flex items-center justify-center
                  bg-primary hover:bg-azwara-medium
                  text-white font-semibold
                  px-5 py-2.5 rounded-xl transition">
            + Tambah Rule
        </a>
    </div>

    {{-- MOBILE LIST --}}
    <div class="space-y-4 sm:hidden">
        @forelse($rules as $rule)
            <div class="rounded-2xl border dark:border-azwara-darker
                        bg-azwara-lightest dark:bg-azwara-darkest
                        p-4 space-y-3">

                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white">
                            {{ $rule->product_type }}
                        </h3>
                        <p class="text-xs mt-1 uppercase
                            inline-block px-2 py-1 rounded-lg
                            bg-gray-100 dark:bg-azwara-darker
                            text-gray-700 dark:text-gray-300">
                            {{ strtoupper($rule->pricing_type) }}
                        </p>
                    </div>
                    @if ($rule->priceable)
                        <span class="text-xs px-2 py-1 rounded-md
                            bg-indigo-100 text-indigo-700">
                            {{ class_basename($rule->priceable_type) }}:
                            {{ $rule->priceable->name ?? $rule->priceable->title ?? '—' }}
                        </span>
                    @else
                        <span class="text-xs px-2 py-1 rounded-md
                            bg-gray-100 text-gray-600">
                            GLOBAL
                        </span>
                    @endif
                    <span class="inline-flex px-3 py-1 rounded-lg text-xs font-semibold
                        {{ $rule->is_active
                            ? 'bg-green-100 text-green-700'
                            : 'bg-gray-100 text-gray-600' }}">
                        {{ $rule->is_active ? 'AKTIF' : 'NONAKTIF' }}
                    </span>
                </div>
                <div class="grid grid-cols-2 gap-3 text-sm text-gray-700 dark:text-gray-300">
                    <div>
                        <p class="text-xs text-gray-500">Range Qty</p>
                        <p class="font-medium">
                            {{ $rule->min_qty }} – {{ $rule->max_qty ?? '∞' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500">Harga</p>
                        <p class="font-semibold text-gray-900 dark:text-azwara-lightest">
                            Rp {{ number_format($rule->price, 0, ',', '.') }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4 pt-2 text-sm">
                    <a href="{{ route('pricing.edit', $rule) }}"
                       class="text-primary font-semibold hover:underline">
                        Edit
                    </a>

                    <form method="POST"
                          action="{{ route('pricing.toggle', $rule) }}"
                          class="sweet-confirm"
                          data-message="Yakin ingin mengubah status pricing rule ini?">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                                class="font-semibold
                                    {{ $rule->is_active
                                        ? 'text-red-600 hover:underline'
                                        : 'text-green-600 hover:underline' }}">
                            {{ $rule->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="text-center text-gray-500 py-10">
                Belum ada pricing rule.
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
                    <th class="px-6 py-4">Produk</th>
                    <th class="px-6 py-4">Tipe</th>
                    <th class="px-6 py-4">Scope</th>
                    <th class="px-6 py-4">Range Qty</th>
                    <th class="px-6 py-4">Harga</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-right"></th>
                </tr>
            </thead>

            <tbody class="divide-y dark:divide-azwara-darker">
                @forelse($rules as $rule)
                    <tr class="align-top">

                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                            {{ $rule->product_type }}
                        </td>

                        <td class="px-6 py-4 dark:text-white">
                            {{ strtoupper($rule->pricing_type) }}
                        </td>
                        <td class="px-6 py-4 text-sm">
                            @if ($rule->priceable)
                                <span class="inline-flex px-2 py-1 rounded-md text-xs
                                    bg-indigo-100 text-indigo-700">
                                    {{ class_basename($rule->priceable_type) }}:
                                    {{ $rule->priceable->name ?? $rule->priceable->title ?? '—' }}
                                </span>
                            @else
                                <span class="inline-flex px-2 py-1 rounded-md text-xs
                                    bg-gray-100 text-gray-600">
                                    GLOBAL
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 dark:text-white">
                            {{ $rule->min_qty }} – {{ $rule->max_qty ?? '∞' }}
                        </td>

                        <td class="px-6 py-4 font-semibold text-gray-900 dark:text-azwara-lightest">
                            Rp {{ number_format($rule->price, 0, ',', '.') }}
                        </td>

                        <td class="px-6 py-4">
                            <span class="inline-flex px-3 py-1 rounded-lg text-xs font-semibold
                                {{ $rule->is_active
                                    ? 'bg-green-100 text-green-700'
                                    : 'bg-gray-100 text-gray-600' }}">
                                {{ $rule->is_active ? 'AKTIF' : 'NONAKTIF' }}
                            </span>
                        </td>

                        <td class="px-6 py-4 text-right space-x-3 whitespace-nowrap">
                            <a href="{{ route('pricing.edit', $rule) }}"
                               class="text-primary font-semibold hover:underline">
                                Edit
                            </a>

                            <form method="POST"
                                  action="{{ route('pricing.toggle', $rule) }}"
                                  class="inline sweet-confirm"
                                  data-message="Yakin ingin mengubah status pricing rule ini?">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        class="font-semibold
                                            {{ $rule->is_active
                                                ? 'text-red-600 hover:underline'
                                                : 'text-green-600 hover:underline' }}">
                                    {{ $rule->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </form>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                            Belum ada pricing rule.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
