@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 px-4 sm:px-6 lg:px-0">

    {{-- HEADER --}}
    <div>
        <h1 class="text-2xl font-bold text-azwara-darker dark:text-white">
            Bonus Produk
        </h1>
        <p class="text-sm text-gray-600 dark:text-gray-400">
            Atur bonus otomatis yang didapat saat membeli produk
        </p>
    </div>

    {{-- MOBILE LIST --}}
    <div class="space-y-4 sm:hidden">
        @forelse($products as $product)
            <div class="rounded-2xl border dark:border-azwara-darker
                        bg-azwara-lightest dark:bg-azwara-darkest
                        p-4 space-y-3">

                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white">
                            {{ $product->name }}
                        </h3>
                        <p class="mt-1 text-xs uppercase
                            inline-block px-2 py-1 rounded-lg
                            bg-gray-100 dark:bg-azwara-darker
                            text-gray-700 dark:text-gray-300">
                            {{ strtoupper($product->type) }}
                        </p>
                    </div>
                </div>

                <div class="text-sm text-gray-700 dark:text-gray-300">
                    <p class="text-xs text-gray-500 mb-1">Bonus</p>
                    @if($product->bonuses->isEmpty())
                        <span class="text-gray-500 dark:text-gray-400">—</span>
                    @else
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($product->bonuses as $bonus)
                                <li>{{ strtoupper($bonus->bonus_type) }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <div class="flex justify-end pt-2">
                    <a href="{{ route('bonuses.edit', $product) }}"
                       class="text-primary font-semibold hover:underline dark:text-azwara-lighter">
                        Atur Bonus
                    </a>
                </div>
            </div>
        @empty
            <div class="text-center text-gray-500 py-10">
                Belum ada produk.
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
                    <th class="px-6 py-4">Bonus</th>
                    <th class="px-6 py-4 text-right"></th>
                </tr>
            </thead>

            <tbody class="divide-y dark:divide-azwara-darker">
                @forelse($products as $product)
                    <tr>

                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                            {{ $product->name }}
                        </td>

                        <td class="px-6 py-4 dark:text-white">
                            {{ strtoupper($product->type) }}
                        </td>

                        <td class="px-6 py-4 text-gray-700 dark:text-gray-300">
                            @if($product->bonuses->isEmpty())
                                <span class="text-gray-500 dark:text-gray-400">—</span>
                            @else
                                <ul class="list-disc list-inside">
                                    @foreach($product->bonuses as $bonus)
                                        <li>{{ strtoupper($bonus->bonus_type) }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </td>

                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('bonuses.edit', $product) }}"
                               class="text-primary font-semibold hover:underline dark:text-azwara-lighter">
                                Atur Bonus
                            </a>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-10 text-center text-gray-500">
                            Belum ada produk.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pt-4">
        {{ $products->links() }}
    </div>

</div>
@endsection
