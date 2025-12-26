<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'thumbnail',
    ];

    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'course_teacher');
    }

    public function meetings()
    {
        return $this->hasMany(Meeting::class)->orderBy('scheduled_at');;
    }

    public function questionCategories()
    {
        return $this->hasMany(QuestionCategory::class);
    }
    public function product()
    {
        return $this->morphOne(Productable::class, 'productable')->with('product');
    }
    public function coursePackage()
    {
        return $this->morphOne(Productable::class, 'productable')
            ->whereHas('product', fn ($q) =>
                $q->where('type', 'course_package')
            )
            ->with('product');
    }
}
