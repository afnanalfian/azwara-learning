@extends('layouts.app')

@section('content')
<a
    href="{{ route('browse.index') }}"
    class="text-smfont-medium text-primary hover:underline dark:text-azwara-lightest">
    ← Kembali
</a>
<div class="max-w-7xl mt-5 mx-auto space-y-8 px-4 sm:px-6">

    {{-- HEADER --}}
    <div class="flex items-start justify-between gap-4">

        {{-- LEFT --}}
        <div class="flex-1 min-w-0">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ $course->name }}
            </h1>

            @if($course->description)
                <p class="text-gray-600 dark:text-gray-400 mt-2">
                    {{ $course->description }}
                </p>
            @endif
        </div>

        {{-- RIGHT: CART --}}
        <a href="{{ route('cart.show') }}"
           class="relative inline-flex items-center justify-center
                  w-11 h-11 rounded-xl shrink-0
                  bg-gray-100 hover:bg-gray-200
                  dark:bg-azwara-darker dark:hover:bg-azwara-dark">

            <svg xmlns="http://www.w3.org/2000/svg"
                 class="w-6 h-6 text-gray-700 dark:text-gray-200"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 3h2l.4 2M7 13h10l4-8H5.4
                         M7 13l-1.5 7h13L17 13
                         M7 13h10
                         M9 21a1 1 0 100-2 1 1 0 000 2
                         M15 21a1 1 0 100-2 1 1 0 000 2" />
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

    {{-- FULL COURSE --}}
    <div class="p-6 rounded-2xl border
                border-gray-200 dark:border-azwara-darker
                bg-white dark:bg-azwara-darkest space-y-4">

        <p class="font-semibold text-primary dark:text-azwara-lighter">
            Harga Full Course:
            Rp {{ number_format(price_for_course_package($course), 0, ',', '.') }}
        </p>

        @if ($course->coursePackage)
            @php
                $productId = $course->coursePackage->product->id;
                $inCart    = in_array($productId, $cartProductIds);
                $owned     = auth()->user()?->hasCourse($course->id);
            @endphp

            @if ($owned)
                <button disabled
                        class="w-full py-3 rounded-xl
                               bg-gray-300 text-gray-600
                               cursor-not-allowed">
                    Sudah Dimiliki
                </button>

            @elseif ($inCart)
                <button disabled
                        class="w-full py-3 rounded-xl
                               bg-gray-400 text-white
                               cursor-not-allowed">
                    Sudah di Keranjang
                </button>

            @else
                <button type="button"
                        data-product-id="{{ $productId }}"
                        class="add-to-cart-btn w-full py-3 rounded-xl
                               bg-primary text-white font-semibold">
                    Beli Full Course + Full Akses
                </button>
            @endif
        @endif
    </div>

    {{-- MEETINGS --}}
    <div class="space-y-4">

        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
            Daftar Pertemuan
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

        @forelse ($meetings as $m)
            <div class="rounded-2xl border
                        border-gray-200 dark:border-azwara-darker
                        bg-white dark:bg-azwara-darkest p-6">

                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                    {{ $m->title }}
                </h3>

                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                    {{ $m->scheduled_at->format('d M Y, H:i') }}
                </p>

                @php
                    $range = price_range_meeting($course);
                @endphp

                <p class="font-semibold text-primary text-sm">
                    Rp {{ number_format($range['min'], 0, ',', '.') }}
                    –
                    Rp {{ number_format($range['max'], 0, ',', '.') }}
                </p>

                {{-- GUNAKAN $m->product (accessor) --}}
                @if ($m->product)
                    @php
                        // $m->product sudah mengembalikan Product model
                        $productId = $m->product->id; // LANGSUNG .id
                        $inCart    = in_array($productId, $cartProductIds);
                        $locked    = in_array($m->course_id, $courseIdsInCart);
                    @endphp

                    @if ($locked)
                        <button disabled
                                class="mt-5 w-full py-3 rounded-xl
                                    bg-gray-300 text-gray-600 cursor-not-allowed">
                            Termasuk Full Course
                        </button>

                    @elseif ($inCart)
                        <button disabled
                                class="mt-5 w-full py-3 rounded-xl
                                    bg-gray-400 text-white cursor-not-allowed">
                            Sudah di Keranjang
                        </button>

                    @else
                        <button type="button"
                                data-product-id="{{ $productId }}"
                                class="add-to-cart-btn mt-5 w-full py-3 rounded-xl
                                    bg-primary text-white">
                            Tambah ke Keranjang
                        </button>
                    @endif
                @endif
            </div>
        @empty
            <p class="text-gray-600 dark:text-gray-400">
                Belum ada pertemuan.
            </p>
        @endforelse
        </div>
    </div>
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
