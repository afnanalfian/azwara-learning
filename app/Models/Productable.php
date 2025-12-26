<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Productable extends Model
{
    protected $fillable = [
        'product_id',
        'productable_type',
        'productable_id',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function productable()
    {
        return $this->morphTo();
    }
    // Untuk meeting
    public function meeting()
    {
        return $this->morphOne(Meeting::class, 'productable');
    }

    // Untuk exam
    public function exam()
    {
        return $this->morphOne(Exam::class, 'productable');
    }

    // Untuk course
    public function course()
    {
        return $this->morphOne(Course::class, 'productable');
    }
}
