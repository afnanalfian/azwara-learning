<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentSetting;
use App\Models\Discount;
use App\Services\DiscountService;
use App\Services\CheckoutService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;

class CheckoutController extends Controller
{
    /**
     * Halaman review checkout
     */
    public function review(Request $request)
    {
        $cart = Cart::where('user_id', $request->user()->id)
            ->where('status', 'active')
            ->with([
                'items.product.bonuses',
                'items.product.productable',
            ])
            ->firstOrFail();

        /**
         * Ambil discount TANPA kode (selectable discount)
         */
        $availableDiscounts = Discount::query()
            ->whereNull('code')
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')
                ->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')
                ->orWhere('ends_at', '>=', now());
            })
            ->orderByDesc('created_at')
            ->get();

        return view(
            'purchase.checkout.review',
            compact('cart', 'availableDiscounts')
        );
    }

    /**
     * Submit checkout
     */
    public function process(
        Request $request,
        CheckoutService $checkoutService
    ) {
        $data = $request->validate([
            'voucher_code' => 'nullable|string',
            'discount_id'  => 'nullable|exists:discounts,id',
        ]);

        if ($data['voucher_code'] && $data['discount_id']) {
            throw ValidationException::withMessages([
                'discount' => 'Pilih salah satu: voucher atau diskon',
            ]);
        }

        $cart = Cart::where('user_id', $request->user()->id)
            ->where('status', 'active')
            ->with('items')
            ->firstOrFail();

        $discount = null;

        if ($data['voucher_code']) {
            $discount = Discount::where('code', $data['voucher_code'])->firstOrFail();
        }

        if ($data['discount_id']) {
            $discount = Discount::findOrFail($data['discount_id']);
        }

        $order = $checkoutService->checkout(
            cart: $cart,
            userId: $request->user()->id,
            discount: $discount
        );

        return redirect()->route('checkout.payment', $order);
    }

    /**
     * Halaman pembayaran
     */
    public function payment(Order $order, Request $request)
    {
        abort_if($order->user_id !== $request->user()->id, 403);
        abort_if($order->status !== 'pending', 403);

        $order->load('items.product');

        $qrisImage   = PaymentSetting::where('key', 'qris_image')->value('value');
        $instruction = PaymentSetting::where('key', 'payment_instruction')->value('value');

        return view('purchase.checkout.payment', compact(
            'order',
            'qrisImage',
            'instruction'
        ));
    }

    /**
     * Upload bukti pembayaran
     */
    public function uploadProof(Order $order, Request $request)
    {
        abort_if($order->user_id !== $request->user()->id, 403);
        abort_if($order->status !== 'pending', 403);

        $validated = $request->validate([
            'proof_image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $path = $validated['proof_image']->store('payments/proofs', 'public');

        $payment = Payment::where('order_id', $order->id)->firstOrFail();

        $payment->update([
            'proof_image' => $path,
            'paid_at'     => now(),
            'status'      => 'paid',
        ]);
        $order->update([
            'status' => 'paid',
        ]);
        $admins = User::role('admin')->get();

        foreach ($admins as $admin) {
            notify_user(
                $admin,
                "Order #{$order->id} baru masuk dan menunggu konfirmasi pembayaran.",
                true,
                route('orders.show', $order)
            );
        }
        return redirect()
            ->route('checkout.waiting', $order)
            ->with('success', 'Bukti pembayaran berhasil diupload');
    }
    public function previewDiscount(
        Request $request,
        DiscountService $discountService
    ): JsonResponse {
        $request->validate([
            'voucher_code' => 'nullable|string',
            'discount_id'  => 'nullable|integer',
        ]);

        $cart = Cart::where('user_id', $request->user()->id)
            ->where('status', 'active')
            ->with('items')
            ->firstOrFail();

        $subtotal = $cart->items->sum(
            fn ($item) => $item->price_snapshot * $item->qty
        );

        try {
            $result = $discountService->preview(
                voucherCode: $request->voucher_code,
                discountId: $request->discount_id,
                subtotal: $subtotal,
                userId: $request->user()->id
            );

            return response()->json([
                'success' => true,
                'data'    => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
    /**
     * Waiting page
     */
    public function waiting(Order $order, Request $request)
    {
        abort_if($order->user_id !== $request->user()->id, 403);

        return view('purchase.checkout.waiting', compact('order'));
    }
}
