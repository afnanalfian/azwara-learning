<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Discount;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Exception;

class CheckoutService
{
    protected DiscountService $discountService;

    public function __construct(DiscountService $discountService)
    {
        $this->discountService = $discountService;
    }

    /**
     * Checkout cart → order
     */
    public function checkout(
        Cart $cart,
        int $userId,
        ?Discount $discount = null
    ): Order {
        if ($cart->status !== 'active') {
            throw new Exception('Cart bukan active');
        }

        if ($cart->items->isEmpty()) {
            throw new Exception('Cart kosong');
        }

        return DB::transaction(function () use ($cart, $userId, $discount) {

            $cart->load('items.product');

            $subtotal = $cart->items->sum(
                fn ($item) => $item->price_snapshot * $item->qty
            );

            $order = Order::create([
                'user_id'      => $cart->user_id,
                'total_amount' => $subtotal,
                'status'       => 'pending',
                'expires_at'   => now()->addHours(2),
            ]);

            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $item->product_id,
                    'qty'        => $item->qty,
                    'price'      => $item->price_snapshot,
                ]);
            }

            // APPLY DISCOUNT (OPTIONAL)
            if ($discount) {
                $this->discountService->applyDiscountToOrder(
                    $order,
                    $discount,
                    $userId
                );
            }

            Payment::create([
                'order_id' => $order->id,
                'method'   => 'manual_qris',
                'status'   => 'waiting',
            ]);

            $cart->update([
                'status' => 'checked_out',
            ]);
            return $order;
        });
    }
}
