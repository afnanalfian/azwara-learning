@extends('layouts.app')

@section('content')
<a href="{{ route('pricing.index') }}"
   class="text-sm font-medium text-primary hover:underline dark:text-azwara-lightest">
    ← Kembali
</a>

<div class="max-w-3xl mx-auto space-y-6">

    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
            Edit Pricing Rule
        </h1>
        <p class="text-sm text-gray-600 dark:text-gray-400">
            Perbarui aturan harga yang sudah ada
        </p>
    </div>

    {{-- UPDATE FORM --}}
    <form method="POST"
          action="{{ route('pricing.update', $pricingRule) }}"
          class="space-y-6 p-6 rounded-2xl border
                 dark:border-azwara-darker
                 bg-white dark:bg-azwara-darkest">
        @csrf
        @method('PUT')

        {{-- PRODUCT TYPE --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                Tipe Produk
            </label>
            <input type="text" disabled
                   value="{{ strtoupper($pricingRule->product_type) }}"
                   class="mt-1 w-full rounded-xl bg-gray-100
                          dark:bg-azwara-darker
                          border-gray-300 dark:text-white">
        </div>

        {{-- PRICING TYPE --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                Tipe Harga
            </label>
            <select name="pricing_type"
                    class="mt-1 w-full rounded-xl border-gray-300
                           dark:bg-azwara-darkest
                           dark:border-azwara-darker
                           dark:text-white">
                <option value="per_unit"
                    @selected($pricingRule->pricing_type === 'per_unit')>
                    Per Unit
                </option>
                <option value="fixed"
                    @selected($pricingRule->pricing_type === 'fixed')>
                    Harga Tetap
                </option>
            </select>
        </div>

        {{-- QTY --}}
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Minimum Qty
                </label>
                <input type="number" name="min_qty" min="1"
                       value="{{ $pricingRule->min_qty }}"
                       class="mt-1 w-full rounded-xl border-gray-300
                              dark:bg-azwara-darkest
                              dark:border-azwara-darker
                              dark:text-white">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Maksimum Qty (opsional)
                </label>
                <input type="number" name="max_qty" min="1"
                       value="{{ $pricingRule->max_qty ?? '' }}"
                       class="mt-1 w-full rounded-xl border-gray-300
                              dark:bg-azwara-darkest
                              dark:border-azwara-darker
                              dark:text-white">
            </div>
        </div>

        {{-- PRICE --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                Harga
            </label>
            <input type="number" name="price" min="0" step="0.01"
                   value="{{ $pricingRule->price }}"
                   class="mt-1 w-full rounded-xl border-gray-300
                          dark:bg-azwara-darkest
                          dark:border-azwara-darker
                          dark:text-white">
        </div>

        {{-- ACTIVE --}}
        <div class="flex items-center gap-3">
            <input type="checkbox" name="is_active" value="1"
                   @checked($pricingRule->is_active)
                   class="rounded text-primary focus:ring-primary">
            <span class="text-sm text-gray-700 dark:text-gray-300">
                Aktifkan pricing rule
            </span>
        </div>

        {{-- ACTIONS --}}
        <div class="pt-4 flex justify-end gap-3">
            <a href="{{ route('pricing.index') }}"
               class="px-5 py-2.5 rounded-xl border
                      text-gray-700 dark:text-gray-300">
                Batal
            </a>

            <button type="submit"
                    class="px-6 py-2.5 rounded-xl
                           bg-primary hover:bg-azwara-medium
                           text-white font-semibold transition">
                Update
            </button>
        </div>
    </form>

    {{-- DELETE --}}
    <form method="POST"
          action="{{ route('pricing.destroy', $pricingRule) }}"
          class="sweet-confirm"
          data-message="Yakin ingin menghapus pricing rule ini secara permanen?">
        @csrf
        @method('DELETE')

        <button type="submit"
                class="px-5 py-2.5 rounded-xl
                       bg-red-600 hover:bg-red-700
                       text-white font-semibold">
            Hapus Permanen
        </button>
    </form>

</div>
@endsection
