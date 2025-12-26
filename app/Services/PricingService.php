<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\PricingRule;
use Illuminate\Database\Eloquent\Model;
use Exception;
use LogicException;
use Illuminate\Support\Facades\Log;


class PricingService
{
    /* ============================================
     | CART TOTAL
     ============================================ */
    public function calculateCartTotal(Cart $cart): array
    {
        $cart->load('items.product.productable');

        $items = [];
        $grandTotal = 0;

        /**
         * 1. HITUNG MEETING PER COURSE
         */
        $meetingPrices = $this->meetingUnitPricesPerCourse($cart);

        foreach ($cart->items as $item) {
            $unitPrice = match ($item->product->type) {
                'meeting' => $meetingPrices[
                    $item->product->productable->id
                ] ?? 0,

                default => $item->price_snapshot,
            };

            $lineTotal = $unitPrice * $item->qty;

            $items[] = [
                'cart_item_id' => $item->id,
                'product_id'   => $item->product_id,
                'qty'          => $item->qty,
                'unit_price'   => $unitPrice,
                'subtotal'     => $lineTotal,
            ];

            $grandTotal += $lineTotal;
        }

        return [
            'items' => $items,
            'total' => $grandTotal,
        ];
    }

    /* ============================================
     | UNIT PRICE NON-MEETING
     ============================================ */
    public function determineUnitPrice(
        string $productType,
        int $qty,
        ?Model $priceable = null
    ): float {

        if ($productType === 'meeting') {
            throw new LogicException(
                'Gunakan meetingUnitPricesPerCourse() untuk product meeting'
            );
        }

        $rule = $this->resolvePricingRule(
            productType: $productType,
            qty: $qty,
            priceable: $priceable
        );

        return $rule->price ?? 0; // Gunakan accessor getPriceAttribute()
    }

    /* ============================================
     | MEETING UNIT PRICE PER COURSE
     ============================================ */
    public function meetingUnitPricesPerCourse(Cart $cart): array
    {
        $cart->loadMissing([
            'items.product.productable.productable'
        ]);

        $meetingItems = $cart->items->filter(
            fn ($item) => $item->product->type === 'meeting'
        );

        $grouped = [];

        foreach ($meetingItems as $item) {
            // Akses meeting melalui productable->productable
            $meeting = $item->product->productable?->productable;

            if (!$meeting || !isset($meeting->course_id)) {
                continue;
            }

            $courseId = $meeting->course_id;

            if (!isset($grouped[$courseId])) {
                $grouped[$courseId] = [
                    'qty' => 0,
                    'course' => null
                ];
            }

            $grouped[$courseId]['qty'] += $item->qty;

            if (!$grouped[$courseId]['course']) {
                $grouped[$courseId]['course'] = \App\Models\Course::find($courseId);
            }
        }

        $prices = [];

        foreach ($grouped as $courseId => $data) {
            if ($courseId === 0 || !$data['course']) continue;

            try {
                $rule = $this->resolvePricingRule(
                    productType: 'meeting',
                    qty: $data['qty'],
                    priceable: $data['course']
                );

                if (!$rule->price_per_unit) {
                    throw new Exception(
                        "Meeting pricing rule invalid for course {$courseId}"
                    );
                }

                $prices[$courseId] = $rule->price_per_unit;
            } catch (\Exception $e) {
                // Log error dan beri harga default
                Log::error('Pricing error for course ' . $courseId, [
                    'error' => $e->getMessage(),
                    'qty' => $data['qty']
                ]);

                $prices[$courseId] = 0;
            }
        }

        return $prices;
    }

    /* ============================================
     | GET PRICEABLE FOR PRODUCT
     | Convert product -> priceable yang sesuai dengan pricing rules
     ============================================ */
    public function getPriceableForProduct($product): ?Model
    {
        if (!$product->productable) {
            return null;
        }

        $productable = $product->productable;

        return match ($product->type) {
            'meeting' => $productable->productable->course ?? null, // Meeting -> Course
            'course_package' => $productable->productable ?? null, // Product -> Course
            'tryout' => $productable->productable ?? null, // Exam dari productable
            'addon' => null, // Addon global
            default => null,
        };
    }

    /* ============================================
     | CORE PRICING RESOLVER
     | priority: specific → global
     ============================================ */
    protected function resolvePricingRule(
        string $productType,
        int $qty,
        ?Model $priceable = null
    ): PricingRule {

        $baseQuery = PricingRule::active()
            ->forProductType($productType)
            ->matchQty($qty);

        /**
         * PRICEABLE-SPECIFIC
         */
        if ($priceable) {
            $rule = (clone $baseQuery)
                ->where('priceable_type', get_class($priceable))
                ->where('priceable_id', $priceable->id)
                ->bestMatch()
                ->first();

            if ($rule) {
                return $rule;
            }
        }

        /**
         * GLOBAL FALLBACK
         */
        $rule = (clone $baseQuery)
            ->whereNull('priceable_type')
            ->whereNull('priceable_id')
            ->bestMatch()
            ->first();

        if (! $rule) {
            throw new Exception(
                "Pricing rule missing for {$productType} (qty={$qty})" .
                ($priceable ? " priceable: " . get_class($priceable) . " #" . $priceable->id : "")
            );
        }

        return $rule;
    }

    /* ============================================
     | FLAT PRICE HELPERS
     ============================================ */
    public function addonPrice(): float
    {
        return $this->resolvePricingRule('addon', 1)->price;
    }

    public function coursePackagePrice(?Model $course = null): float
    {
        return $this->resolvePricingRule(
            'course_package',
            1,
            $course
        )->price;
    }
}
