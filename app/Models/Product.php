<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'type',
        'name',
        'description',
        'is_active',
    ];

    /* ================= RELATIONS ================= */

    public function productable()
    {
        return $this->hasOne(Productable::class);
    }
    public function productables()
    {
        return $this->hasMany(Productable::class);
    }
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function bonuses()
    {
        return $this->hasMany(ProductBonus::class);
    }
    public function hasBonus(string $type, $id = null): bool
    {
        return $this->bonuses()
            ->where('bonus_type', $type)
            ->when($id, fn($q) => $q->where('bonus_id', $id))
            ->exists();
    }
    /* ================= PRICING RULE ================= */
    function price_for_tryout()
    {
        $rule = PricingRule::where('product_type', 'tryout')
            ->where('is_active', true)
            ->orderBy('min_qty')
            ->first();

        return $rule?->fixed_price ?? 0;
    }

    function price_range_meeting()
    {
        $min = PricingRule::where('product_type', 'meeting')->min('price_per_unit');
        $max = PricingRule::where('product_type', 'meeting')->max('price_per_unit');

        return [
            'min' => $min ?? 0,
            'max' => $max ?? 0,
        ];
    }
    /**
     * Get the actual model (Meeting, Exam, Course) through productable
     */
    public function getActualModelAttribute()
    {
        if (!$this->productable) {
            return null;
        }

        return $this->productable->productable;
    }

    /**
     * Get course ID if this is a meeting
     */
    public function getCourseIdAttribute()
    {
        if ($this->type !== 'meeting') {
            return null;
        }

        $meeting = $this->getActualModelAttribute();
        return $meeting?->course_id ?? null;
    }
}
