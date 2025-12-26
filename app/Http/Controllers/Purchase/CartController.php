<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Productable;
use App\Models\Meeting;
use App\Models\UserEntitlement;
use App\Services\PricingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    /**
     * Tampilkan cart aktif user
     */
    public function show(Request $request)
    {
        $cart = $this->getActiveCart($request->user()->id);
        $cart->load('items.product');

        $subtotal = 0;

        foreach ($cart->items as $item) {
            $subtotal += ($item->price_snapshot * $item->qty);
        }

        return view('purchase.cart.show', compact(
            'cart',
            'subtotal'
        ));
    }
    protected function cartHasCourse($cart): bool
    {
        return $cart->items()
            ->whereHas('product', fn ($q) =>
                $q->where('type', 'course_package')
            )
            ->exists();
    }

    /**
     * Add product ke cart - FIXED VERSION
     */
    public function add(
        Request $request,
        Product $product,
        PricingService $pricingService
    ) {
        $userId = $request->user()->id;
        $cart   = $this->getActiveCart($userId);

        /**
         * 0. SUDAH DIMILIKI
         */
        if ($this->alreadyOwned($userId, $product)) {
            return response()->json([
                'message' => 'Produk ini sudah kamu miliki'
            ], 422);
        }

        /**
         * 1. BLOK TRYOUT JIKA SUDAH ADA COURSE PACKAGE
         */
        if (
            $product->type === 'tryout' &&
            $cart->items()
                ->whereHas('product', fn ($q) =>
                    $q->where('type', 'course_package')
                )
                ->exists()
        ) {
            return response()->json([
                'message' => 'Tryout sudah termasuk dalam Full Course'
            ], 422);
        }

        /**
         * 2. BLOK MEETING JIKA COURSE YANG SAMA SUDAH ADA COURSE PACKAGE
         */
        if ($product->type === 'meeting') {
            // Dapatkan meeting dari product
            $productable = $product->productable;
            $meeting = $productable?->productable;
            $courseId = $meeting?->course_id;

            if ($courseId) {
                // Cari semua meeting di course ini melalui Meeting model
                $meetingIds = Meeting::where('course_id', $courseId)
                    ->pluck('id')
                    ->toArray();

                if (!empty($meetingIds)) {
                    // Cari product_id dari meeting-meeting tersebut
                    $meetingProductIds = Productable::where('productable_type', Meeting::class)
                        ->whereIn('productable_id', $meetingIds)
                        ->pluck('product_id')
                        ->unique()
                        ->toArray();

                    if (!empty($meetingProductIds)) {
                        $cart->items()
                            ->whereIn('product_id', $meetingProductIds)
                            ->delete();
                    }
                }
            }
        }

        /**
         * 3. HITUNG QTY
         */
        $qty = (int) $request->input('qty', 1);

        /**
         * 4. PRICING HARUS LOLOS SEBELUM TRANSACTION
         */
        try {
            if ($product->type === 'meeting') {
                // Tidak dihitung di sini, nanti repricing per-course
                $unitPrice = null;
            } else {
                $priceable = $pricingService->getPriceableForProduct($product);

                $unitPrice = $pricingService->determineUnitPrice(
                    $product->type,
                    $qty,
                    $priceable
                );
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }

        /**
         * 5. TRANSACTION
         */
        DB::transaction(function () use (
            $product,
            $cart,
            $qty,
            $unitPrice,
            $pricingService
        ) {
            /**
             * A. JIKA TAMBAH COURSE PACKAGE
             */
            if ($product->type === 'course_package') {
                // Dapatkan course dari product
                $productable = $product->productable;
                $course = $productable?->productable;

                // HAPUS MEETING COURSE INI
                if ($course) {
                    // Cari semua meeting di course ini
                    $meetingIds = Meeting::where('course_id', $course->id)
                        ->pluck('id')
                        ->toArray();

                    if (!empty($meetingIds)) {
                        // Cari product_id dari meeting-meeting tersebut
                        $meetingProductIds = Productable::where('productable_type', Meeting::class)
                            ->whereIn('productable_id', $meetingIds)
                            ->pluck('product_id')
                            ->unique()
                            ->toArray();

                        if (!empty($meetingProductIds)) {
                            $cart->items()
                                ->whereIn('product_id', $meetingProductIds)
                                ->delete();
                        }
                    }
                }

                // HAPUS TRYOUT
                $cart->items()
                    ->whereHas('product', fn ($q) =>
                        $q->where('type', 'tryout')
                    )
                    ->delete();

                // HAPUS ADDON
                $cart->items()
                    ->whereHas('product', fn ($q) =>
                        $q->where('type', 'addon')
                    )
                    ->delete();
            }

            /**
             * B. INSERT / UPDATE ITEM
             */
            $cartItem = CartItem::updateOrCreate(
                [
                    'cart_id'    => $cart->id,
                    'product_id' => $product->id,
                ],
                [
                    'qty'            => $qty,
                    'price_snapshot' => $unitPrice ?? 0,
                ]
            );

            /**
             * C. REPRICE MEETING PER COURSE
             */
            if ($product->type === 'meeting') {
                $prices = $pricingService->meetingUnitPricesPerCourse($cart);

                // Update harga semua meeting di cart
                $cart->load('items.product.productable.productable');

                foreach ($cart->items as $item) {
                    if ($item->product->type !== 'meeting') {
                        continue;
                    }

                    $meeting = $item->product->productable?->productable;
                    $courseId = $meeting?->course_id ?? 0;

                    if ($courseId && isset($prices[$courseId])) {
                        $item->update([
                            'price_snapshot' => $prices[$courseId],
                        ]);
                    }
                }
            }
        });

        return response()->json([
            'cart_count' => $cart->items()->sum('qty'),
        ]);
    }

    public function updateQty(
        Request $request,
        CartItem $cartItem,
        PricingService $pricingService
    ) {
        $this->authorizeCartItem($request->user()->id, $cartItem);

        $qty = (int) $request->validate([
            'qty' => 'required|integer|min:1'
        ])['qty'];

        DB::transaction(function () use ($cartItem, $qty, $pricingService) {

            $cartItem->update(['qty' => $qty]);

            $cart = $cartItem->cart;

            if ($cartItem->product->type === 'meeting') {

                $prices = $pricingService->meetingUnitPricesPerCourse($cart);

                $cart->load('items.product.productable');

                foreach ($cart->items as $item) {
                    if ($item->product->type !== 'meeting') {
                        continue;
                    }

                    $courseId = $item->product->productable->course_id;

                    $item->update([
                        'price_snapshot' => $prices[$courseId] ?? 0,
                    ]);
                }

            } else {

                $unitPrice = $pricingService->determineUnitPrice(
                    $cartItem->product->type,
                    $qty,
                    $cartItem->product->productable
                );

                $cartItem->update([
                    'price_snapshot' => $unitPrice,
                ]);
            }
        });

        return back()->with('success', 'Jumlah item diperbarui');
    }

    /**
     * Remove item dari cart
     */
    public function remove(Request $request, CartItem $cartItem)
    {
        $this->authorizeCartItem($request->user()->id, $cartItem);

        DB::transaction(function () use ($cartItem) {
            $cartItem->delete();
        });

        return back()->with('success', 'Item dihapus dari cart');
    }



    /* =======================================================
       INTERNAL HELPERS
       ======================================================= */

    protected function getActiveCart(int $userId): Cart
    {
        return Cart::firstOrCreate([
            'user_id' => $userId,
            'status'  => 'active',
        ]);
    }


    protected function alreadyOwned(int $userId, Product $product): bool
    {
        // 1. Jika user sudah punya FULL COURSE
        if (in_array($product->type, ['meeting', 'tryout'])) {
            $hasCourse = UserEntitlement::where('user_id', $userId)
                ->where('entitlement_type', 'course')
                ->exists();

            if ($hasCourse) {
                return true;
            }
        }

        // 2. Cek entitlement normal
        $ids = $product->productables()->pluck('productable_id');

        if ($ids->isEmpty()) {
            return false;
        }

        return UserEntitlement::where('user_id', $userId)
            ->where('entitlement_type', $this->mapType($product->type))
            ->whereIn('entitlement_id', $ids)
            ->exists();
    }


    protected function mapType(string $type): string
    {
        return match ($type) {
            'meeting'        => 'meeting',
            'course_package' => 'course',
            'tryout'         => 'tryout',
            'addon'          => 'quiz',
        };
    }


    protected function authorizeCartItem(int $userId, CartItem $cartItem): void
    {
        abort_if(
            $cartItem->cart->user_id !== $userId,
            403
        );
    }
    protected function userHasQuiz(int $userId): bool
    {
        return UserEntitlement::where('user_id', $userId)
            ->where('entitlement_type', 'quiz')
            ->exists();
    }
    protected function cartHasAddonQuiz(Cart $cart): bool
    {
        return $cart->items()
            ->whereHas('product', fn ($q) =>
                $q->where('type', 'addon')
            )
            ->exists();
    }
    public function addAddonQuiz(
        Request $request,
        PricingService $pricingService
    ) {
        $user = $request->user();

        // Ambil cart aktif
        $cart = $this->getActiveCart($user->id);
        $cart->load('items.product');

        /**
         *  User sudah punya akses quiz
         */
        if ($this->userHasQuiz($user->id)) {
            return back()->withErrors([
                'addon' => 'Kamu sudah memiliki akses Quiz.',
            ]);
        }

        /**
         *  Cart memiliki course package → addon tidak boleh
         */
        if ($cart->items->contains(
            fn ($item) => $item->product->type === 'course_package'
        )) {
            return back()->withErrors([
                'addon' => 'Quiz sudah termasuk dalam paket Course.',
            ]);
        }

        /**
         *  Addon quiz sudah ada di cart
         */
        if ($cart->items->contains(
            fn ($item) => $item->product->type === 'addon'
        )) {
            return back()->withErrors([
                'addon' => 'Addon Quiz sudah ada di keranjang.',
            ]);
        }

        /**
         * Ambil product addon
         */
        $addonProduct = Product::where('type', 'addon')->first();

        if (! $addonProduct) {
            abort(500, 'Produk Addon Quiz belum tersedia.');
        }

        /**
         * Ambil harga dari PricingService (SATU-SATUNYA sumber harga)
         */
        $price = $pricingService->addonPrice();

        /**
         * Simpan ke cart (qty = 1, harga FIX)
         */
        DB::transaction(function () use ($cart, $addonProduct, $price) {

            $cart->items()->create([
                'product_id'     => $addonProduct->id,
                'qty'            => 1,
                'price_snapshot' => $price,
            ]);
        });
        toast('success', 'Addon Quiz berhasil ditambahkan.');
        return back();
    }


}
