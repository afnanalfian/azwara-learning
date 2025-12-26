@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-10">

    {{-- HEADER --}}
    <div class="flex items-start justify-between gap-4">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
            Etalase Pembelajaran
        </h1>

        {{-- CART --}}
        <a id="cart-icon" href="{{ route('cart.show') }}"
           class="relative inline-flex items-center justify-center
                  w-11 h-11 rounded-xl
                  bg-gray-100 hover:bg-gray-200
                  dark:bg-azwara-darker dark:hover:bg-azwara-dark
                  transition">

            <svg xmlns="http://www.w3.org/2000/svg"
                 class="w-6 h-6 text-gray-700 dark:text-gray-200"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 3h2l.4 2M7 13h10l4-8H5.4
                         M7 13l-1.5 7h13L17 13
                         M7 13h10
                         M9 21a1 1 0 100-2
                         M15 21a1 1 0 100-2" />
            </svg>

            <span id="cart-count"
                  class="absolute -top-1.5 -right-1.5
                         min-w-[1.25rem] h-5 px-1
                         bg-red-600 text-white text-xs font-semibold
                         rounded-full flex items-center justify-center">
                {{ auth()->user()?->cart?->items()->sum('qty') ?? 0 }}
            </span>
        </a>

    </div>
    {{-- =========================
    EMPTY STATE (ALL OWNED)
    ========================== --}}
    @if($courses->isEmpty() && $tryouts->isEmpty())
        <div class="rounded-2xl border border-dashed
                    border-emerald-300 dark:border-emerald-700
                    bg-emerald-50 dark:bg-emerald-900/20
                    p-10 text-center">

            <h2 class="text-xl font-semibold text-emerald-700 dark:text-emerald-300">
                🎉 Anda sudah memiliki akses semua produk pembelajaran
            </h2>

            <p class="mt-2 text-sm text-emerald-600 dark:text-emerald-400">
                Semua course, meeting, dan tryout sudah terbuka.
                Silakan lanjutkan belajar dan kerjakan ujian yang tersedia.
            </p>

            <a href="{{ route('dashboard.redirect') }}"
            class="inline-block mt-6 px-6 py-3 rounded-xl
                    bg-emerald-600 hover:bg-emerald-700
                    text-white font-semibold transition">
                Ke Dashboard
            </a>
        </div>
    @endif
    {{-- =========================
       COURSE PACKAGE
    ========================== --}}
    @if($courses->isNotEmpty())
        <section class="space-y-4">

            <h2 class="text-lg font-semibold dark:text-azwara-lighter">
                Paket Full Course
            </h2>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">

                @foreach($courses as $course)
                    <div class="rounded-2xl border border-gray-200 dark:border-azwara-darker
                                bg-white dark:bg-azwara-darkest p-6 shadow-sm">

                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                            {{ $course->name }}
                        </h3>

                        @if($course->description)
                            <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-3 mb-3">
                                {{ $course->description }}
                            </p>
                        @endif
                        <p class="font-semibold text-primary dark:text-azwara-lighter text-sm">
                            @php
                                $range = price_range_meeting($course);
                            @endphp
                            Harga Pertemuan:
                            Rp {{ number_format($range['min'], 0, ',', '.') }}
                            –
                            Rp {{ number_format($range['max'], 0, ',', '.') }}
                        </p>

                        <a href="{{ route('browse.course', $course->id) }}"
                           class="mt-5 block text-center rounded-xl
                                  bg-gray-200 hover:bg-gray-300
                                  dark:bg-azwara-darker dark:hover:bg-azwara-dark
                                  text-gray-800 dark:text-gray-200
                                  font-semibold py-3 transition">
                            Detail Pertemuan
                        </a>

                    </div>
                @endforeach

            </div>
        </section>
    @endif


    {{-- =========================
    TRYOUT
    ========================== --}}
    @if($tryouts->isNotEmpty())
        <section class="space-y-4">

            <h2 class="text-lg font-semibold dark:text-azwara-lighter">
                Tryout
            </h2>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">

                @foreach($tryouts as $t)
                    <div class="rounded-2xl border border-gray-200 dark:border-azwara-darker
                                bg-white dark:bg-azwara-darkest p-6 shadow-sm">

                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                            {{ $t->title }}
                        </h3>

                        @if($t->description)
                            <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-3 mb-3">
                                {{ $t->description }}
                            </p>
                        @endif

                        <p class="font-semibold text-primary dark:text-azwara-lighter text-sm">
                            Rp {{ number_format(price_for_tryout($t), 0, ',', '.') }}
                        </p>

                        {{-- GUNAKAN ACCESSOR $t->product (bukan $t->productable) --}}
                        @if ($t->product)
                            @php
                                // AMBIL PRODUCT_ID DARI ACCESSOR
                                $productId = $t->product->id;
                                $inCart    = in_array($productId, $cartProductIds);

                                // CEK JIKA TRYOUT INI MILIK COURSE TERTENTU
                                $courseId  = optional($t->owner)->id;
                                $locked    = $courseId
                                    ? in_array($courseId, $courseIdsInCart)
                                    : false;
                            @endphp

                            @if ($locked)
                                <button disabled
                                        class="mt-5 block w-full rounded-xl
                                            bg-gray-300 text-gray-600
                                            cursor-not-allowed">
                                    Termasuk Full Course
                                </button>

                            @elseif ($inCart)
                                <button disabled
                                        class="mt-5 block w-full rounded-xl
                                            bg-gray-400 text-white
                                            cursor-not-allowed">
                                    Sudah di Keranjang
                                </button>

                            @else
                                <button type="button"
                                        data-product-id="{{ $productId }}"
                                        class="add-to-cart-btn mt-5 block w-full rounded-xl
                                            bg-primary text-white py-3">
                                    Tambah ke Cart
                                </button>
                            @endif
                        @else
                            <p class="text-sm text-red-500 mt-5">
                                Produk tidak tersedia
                            </p>
                        @endif

                    </div>
                @endforeach

            </div>
        </section>
    @endif

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('click', async function (e) {
    const button = e.target.closest('.add-to-cart-btn');
    if (!button || button.disabled) return;

    e.preventDefault();

    const productId = button.dataset.productId;
    const cartIcon  = document.querySelector('[href="{{ route('cart.show') }}"]');

    button.disabled = true;

    try {
        const res = await fetch(`/cart/add/${productId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document
                    .querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
        });

        const data = await res.json();
        if (!res.ok) throw data;

        // ===============================
        // UPDATE CART BADGE
        // ===============================
        const badge = document.getElementById('cart-count');
        if (badge) badge.textContent = data.cart_count;

        // ===============================
        // ANIMASI KE CART
        // ===============================
        if (cartIcon) {
            const clone = button.cloneNode(true);
            const start = button.getBoundingClientRect();
            const end   = cartIcon.getBoundingClientRect();

            clone.style.position = 'fixed';
            clone.style.left = start.left + 'px';
            clone.style.top = start.top + 'px';
            clone.style.width = start.width + 'px';
            clone.style.zIndex = 9999;
            clone.style.transition = 'all 0.6s ease-in-out';
            clone.style.pointerEvents = 'none';

            document.body.appendChild(clone);

            requestAnimationFrame(() => {
                clone.style.left = end.left + 'px';
                clone.style.top  = end.top + 'px';
                clone.style.transform = 'scale(0.2)';
                clone.style.opacity = '0';
            });

            setTimeout(() => clone.remove(), 650);
        }

        // ===============================
        // FEEDBACK BUTTON
        // ===============================
        button.textContent = 'Sudah di Keranjang';
        button.classList.remove('bg-primary');
        button.classList.add('bg-gray-400', 'cursor-not-allowed');

        notify('success', 'Berhasil menambahkan ke keranjang');

    } catch (err) {
        button.disabled = false;
        notify('error', err.message ?? 'Gagal menambahkan ke keranjang');
    }
});

/* ===============================
   SIMPLE NOTIFICATION
================================ */
function notify(type, message) {
    const el = document.createElement('div');

    el.textContent = message;
    el.className = `
        fixed top-24 right-6 z-[9999]
        px-4 py-3 rounded-xl shadow-lg text-white
        ${type === 'success' ? 'bg-emerald-600' : 'bg-red-600'}
    `;

    document.body.appendChild(el);

    setTimeout(() => el.remove(), 3000);
}
</script>
@endpush
