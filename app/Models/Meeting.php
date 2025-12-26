<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Meeting extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'slug',
        'scheduled_at',
        'started_at',
        'zoom_link',
        'status',
        'created_by',
    ];
    protected $casts = [
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function material()
    {
        return $this->hasOne(MeetingMaterial::class);
    }
    public function exam()
    {
        return $this->morphOne(Exam::class, 'owner')
            ->where('type', 'post_test');
    }
    public function attendances()
    {
        return $this->hasMany(MeetingAttendance::class);
    }
    public function video()
    {
        return $this->hasOne(MeetingVideo::class);
    }
    // Untuk observer dan productable access
    public function productable()
    {
        return $this->morphOne(Productable::class, 'productable');
    }
    public function product()
    {
        return $this->morphOne(Productable::class, 'productable')->with('product');
    }
    // Untuk query langsung ke product
    public function productRelation()
    {
        return $this->hasOneThrough(
            Product::class,
            Productable::class,
            'productable_id',
            'id',
            'id',
            'product_id'
        )->where('productable_type', static::class);
    }

    /**
     * Accessor untuk mendapatkan Product langsung
     * Bisa melalui productRelation atau productable
     */
    public function getProductAttribute()
    {
        // Coba via productRelation dulu
        if ($this->relationLoaded('productRelation')) {
            return $this->productRelation;
        }

        // Fallback via productable
        if (!$this->relationLoaded('productable')) {
            $this->load('productable.product');
        }

        return $this->productable?->product;
    }
}
