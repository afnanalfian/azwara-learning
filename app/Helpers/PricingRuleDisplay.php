<?php

use App\Models\PricingRule;
use Illuminate\Database\Eloquent\Model;

/**
 * Harga fixed tryout
 * (specific → global)
 */
function price_for_tryout(?Model $tryout = null): float
{
    $query = PricingRule::active()
        ->forProductType('tryout')
        ->matchQty(1)
        ->bestMatch();

    if ($tryout) {
        $rule = (clone $query)
            ->forPriceable($tryout)
            ->first();

        if ($rule) {
            return $rule->fixed_price ?? 0;
        }
    }

    return $query->global()->value('fixed_price') ?? 0;
}

/**
 * Range harga meeting (UNTUK BROWSE)
 * support specific course → global
 */
function price_range_meeting(?Model $course = null): array
{
    $base = PricingRule::active()
        ->forProductType('meeting');

    if ($course) {
        $specific = (clone $base)
            ->forPriceable($course)
            ->get();

        if ($specific->isNotEmpty()) {
            return [
                'min' => $specific->min('price_per_unit') ?? 0,
                'max' => $specific->max('price_per_unit') ?? 0,
            ];
        }
    }

    return [
        'min' => $base->global()->min('price_per_unit') ?? 0,
        'max' => $base->global()->max('price_per_unit') ?? 0,
    ];
}

/**
 * Harga meeting berdasarkan qty & course
 */
function meeting_price_by_qty(
    int $qty,
    ?Model $course = null
): float {

    $query = PricingRule::active()
        ->forProductType('meeting')
        ->matchQty($qty)
        ->bestMatch();

    if ($course) {
        $rule = (clone $query)
            ->forPriceable($course)
            ->first();

        if ($rule) {
            return $rule->price_per_unit ?? 0;
        }
    }

    return $query
        ->global()
        ->value('price_per_unit') ?? 0;
}

/**
 * Harga full course
 * (specific course → global)
 */
function price_for_course_package(?Model $course = null): float
{
    $query = PricingRule::active()
        ->forProductType('course_package')
        ->matchQty(1)
        ->bestMatch();

    if ($course) {
        $rule = (clone $query)
            ->forPriceable($course)
            ->first();

        if ($rule) {
            return $rule->fixed_price ?? 0;
        }
    }

    return $query->global()->value('fixed_price') ?? 0;
}

/**
 * Harga addon quiz
 */
function price_for_addon(): float
{
    return PricingRule::active()
        ->forProductType('addon')
        ->matchQty(1)
        ->global()
        ->value('fixed_price') ?? 0;
}
