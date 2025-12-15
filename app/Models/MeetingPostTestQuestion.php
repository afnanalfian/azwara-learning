<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MeetingPostTestQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_test_id',
        'question_id',
        'order',
    ];

    public function postTest()
    {
        return $this->belongsTo(MeetingPostTest::class, 'post_test_id');
    }
    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
