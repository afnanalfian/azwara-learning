<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'thumbnail',
        'description',
    ];
    public function getRouteKeyName()
    {
        return 'slug';
    }
    public function materials()
    {
        return $this->hasMany(QuestionMaterial::class, 'category_id');
    }
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->withTrashed()
            ->where($field ?? $this->getRouteKeyName(), $value)
            ->firstOrFail();
    }
}
