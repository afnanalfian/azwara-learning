<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class PricingRule extends Model
{
    protected $fillable = [
        'product_type',      // meeting | course_package | tryout
        'min_qty',
        'max_qty',
        'price_per_unit',
        'fixed_price',
        'is_active',
        'priceable_type',
        'priceable_id',
    ];

    /* =====================================
     | RELATION
     ===================================== */
    public function priceable()
    {
        return $this->morphTo();
    }

    /* =====================================
     | SCOPES
     ===================================== */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeForProductType(Builder $query, string $type): Builder
    {
        return $query->where('product_type', $type);
    }

    public function scopeGlobal(Builder $query): Builder
    {
        return $query
            ->whereNull('priceable_type')
            ->whereNull('priceable_id');
    }

    public function scopeForPriceable(
        Builder $query,
        Model $priceable
    ): Builder {
        return $query
            ->where('priceable_type', $priceable::class)
            ->where('priceable_id', $priceable->getKey());
    }

    public function scopeMatchQty(Builder $query, int $qty): Builder
    {
        return $query
            ->where(function ($q) use ($qty) {
                $q->whereNull('min_qty')
                  ->orWhere('min_qty', '<=', $qty);
            })
            ->where(function ($q) use ($qty) {
                $q->whereNull('max_qty')
                  ->orWhere('max_qty', '>=', $qty);
            });
    }

    public function scopeBestMatch(Builder $query): Builder
    {
        return $query->orderByDesc('min_qty');
    }

    /* =====================================
     | ACCESSORS (UI / DEBUG)
     ===================================== */
    public function getPricingTypeAttribute(): string
    {
        return $this->price_per_unit !== null
            ? 'per_unit'
            : 'fixed';
    }

    public function getPriceAttribute(): float
    {
        return (float) (
            $this->price_per_unit
            ?? $this->fixed_price
            ?? 0
        );
    }
}
