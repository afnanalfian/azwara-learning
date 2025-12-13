<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'material_id',
        'type',
        'question_text',
        'image',
        'explanation',
    ];

    public function material()
    {
        return $this->belongsTo(QuestionMaterial::class, 'material_id');
    }

    public function options()
    {
        return $this->hasMany(QuestionOption::class, 'question_id');
    }
}
