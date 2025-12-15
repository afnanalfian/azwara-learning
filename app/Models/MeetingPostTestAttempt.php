<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MeetingPostTestAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_test_id',
        'user_id',
        'started_at',
        'submitted_at',
        'duration_seconds',
        'score',
        'is_submitted',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'is_submitted' => 'boolean',
    ];

    /* ================= RELATIONS ================= */

    public function postTest()
    {
        return $this->belongsTo(MeetingPostTest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function answers()
    {
        return $this->hasMany(MeetingPostTestAnswer::class, 'attempt_id');
    }

    /* ================= HELPERS ================= */

    public function hasStarted(): bool
    {
        return ! is_null($this->started_at);
    }

    public function isFinished(): bool
    {
        return $this->is_submitted;
    }
}
