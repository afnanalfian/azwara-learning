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
        return $this->hasMany(Meeting::class);
    }

    public function packages()
    {
        return $this->hasMany(Package::class);
    }

    public function questionCategories()
    {
        return $this->hasMany(QuestionCategory::class);
    }

    public function tryouts()
    {
        return $this->hasMany(Tryout::class);
    }

    public function dailyQuizzes()
    {
        return $this->hasMany(DailyQuiz::class);
    }
}
