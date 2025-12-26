@extends('layouts.app')

@section('content')
<a href="{{ route('pricing.index') }}"
   class="text-sm font-medium text-primary hover:underline dark:text-azwara-lightest">
    ← Kembali
</a>

<div class="max-w-3xl mx-auto space-y-6">

    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-azwara-lightest">
            Tambah Pricing Rule
        </h1>
        <p class="text-sm text-gray-600 dark:text-gray-400">
            Atur harga global atau khusus untuk course / tryout tertentu
        </p>
    </div>

    <form method="POST"
          action="{{ route('pricing.store') }}"
          class="space-y-6 p-6 rounded-2xl border
                 dark:border-azwara-darker
                 bg-white dark:bg-azwara-darkest">
        @csrf

        {{-- Tampilkan error validation --}}
        @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-4">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- Tampilkan session messages --}}
        @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-4">
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-4">
            {{ session('error') }}
        </div>
        @endif

        {{-- PRODUCT TYPE --}}
        <div>
            <label class="block text-sm font-medium">Tipe Produk</label>
            <select name="product_type" id="product_type"
                    class="mt-1 w-full rounded-xl border-gray-300
                           dark:bg-azwara-darkest dark:border-azwara-darker">
                <option value="meeting">Meeting</option>
                <option value="tryout">Tryout</option>
                <option value="course_package">Course Package</option>
                <option value="addon">Addon</option>
            </select>
        </div>

        {{-- SCOPE --}}
        <div>
            <label class="block text-sm font-medium">Berlaku Untuk</label>
            <select name="price_scope" id="price_scope"
                    class="mt-1 w-full rounded-xl border-gray-300
                           dark:bg-azwara-darkest dark:border-azwara-darker">
                <option value="global">Global (Semua)</option>
                <option value="specific">Spesifik</option>
            </select>
        </div>

        {{-- SPECIFIC TARGET --}}
        <div id="specific-wrapper" class="hidden space-y-4">
            {{-- Hanya akan menampilkan SATU input berdasarkan product_type --}}
            <div id="course-select" class="hidden">
                <label class="block text-sm font-medium">
                    Course
                </label>
                <select name="priceable_id" id="course-priceable"
                        class="mt-1 w-full rounded-xl border-gray-300
                               dark:bg-azwara-darkest dark:border-azwara-darker">
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}">
                            {{ $course->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- TRYOUT --}}
            <div id="tryout-select" class="hidden">
                <label class="block text-sm font-medium">
                    Tryout
                </label>
                <select name="priceable_id" id="tryout-priceable"
                        class="mt-1 w-full rounded-xl border-gray-300
                               dark:bg-azwara-darkest dark:border-azwara-darker">
                    @foreach($tryouts as $tryout)
                        <option value="{{ $tryout->id }}">
                            {{ $tryout->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- HIDDEN: Priceable Type (akan diisi oleh JavaScript) --}}
            <input type="hidden" name="priceable_type" id="priceable-type-input" value="">
        </div>

        {{-- PRICING TYPE --}}
        <div>
            <label class="block text-sm font-medium">Tipe Harga</label>
            <select name="pricing_type" id="pricing_type"
                    class="mt-1 w-full rounded-xl border-gray-300
                           dark:bg-azwara-darkest dark:border-azwara-darker">
                <option value="per_unit">Per Unit</option>
                <option value="fixed">Harga Tetap</option>
            </select>
        </div>

        {{-- QTY RANGE --}}
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium">Minimum Qty</label>
                <input type="number" name="min_qty" min="1"
                       class="mt-1 w-full rounded-xl border-gray-300
                              dark:bg-azwara-darkest dark:border-azwara-darker">
            </div>

            <div>
                <label class="block text-sm font-medium">
                    Maksimum Qty (opsional)
                </label>
                <input type="number" name="max_qty" min="1"
                       class="mt-1 w-full rounded-xl border-gray-300
                              dark:bg-azwara-darkest dark:border-azwara-darker">
            </div>
        </div>

        {{-- PRICE --}}
        <div>
            <label class="block text-sm font-medium">Harga</label>
            <input type="number" name="price" id="price"
                   min="0" step="0.01"
                   class="mt-1 w-full rounded-xl border-gray-300
                          dark:bg-azwara-darkest dark:border-azwara-darker">
        </div>

        {{-- ACTIVE --}}
        <div class="flex items-center gap-3">
            <input type="checkbox" name="is_active" value="1" checked>
            <span class="text-sm">Aktifkan pricing rule</span>
        </div>

        <div class="pt-4 flex justify-end gap-3">
            <a href="{{ route('pricing.index') }}"
               class="px-5 py-2.5 rounded-xl border">
                Batal
            </a>

            <button type="submit"
                    class="px-6 py-2.5 rounded-xl
                           bg-primary text-white font-semibold">
                Simpan
            </button>
        </div>

    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const productType = document.getElementById('product_type');
    const scope       = document.getElementById('price_scope');
    const wrapper     = document.getElementById('specific-wrapper');
    const courseBox   = document.getElementById('course-select');
    const tryoutBox   = document.getElementById('tryout-select');
    const priceableTypeInput = document.getElementById('priceable-type-input');

    function updateScope() {
        // Reset semua
        wrapper.classList.add('hidden');
        courseBox.classList.add('hidden');
        tryoutBox.classList.add('hidden');
        priceableTypeInput.value = '';

        // Disable semua select
        document.getElementById('course-priceable')?.setAttribute('disabled', 'disabled');
        document.getElementById('tryout-priceable')?.setAttribute('disabled', 'disabled');

        // Jika bukan specific, selesai
        if (scope.value !== 'specific') return;

        // Tampilkan wrapper
        wrapper.classList.remove('hidden');

        // Tentukan tipe product
        const selectedProduct = productType.value;

        if (selectedProduct === 'meeting' || selectedProduct === 'course_package') {
            // Tampilkan course, sembunyikan tryout
            courseBox.classList.remove('hidden');
            document.getElementById('course-priceable')?.removeAttribute('disabled');
            priceableTypeInput.value = '{{ addslashes(\App\Models\Course::class) }}';
        }
        else if (selectedProduct === 'tryout') {
            // Tampilkan tryout, sembunyikan course
            tryoutBox.classList.remove('hidden');
            document.getElementById('tryout-priceable')?.removeAttribute('disabled');
            priceableTypeInput.value = '{{ addslashes(\App\Models\Exam::class) }}';
        }
        // Untuk addon, tidak ada specific target
    }

    productType.addEventListener('change', updateScope);
    scope.addEventListener('change', updateScope);

    // Inisialisasi pertama kali
    updateScope();
});
</script>
@endpush
