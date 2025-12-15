<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MeetingPostTest extends Model
{
    use HasFactory;

    protected $fillable = [
        'meeting_id',
        'duration_minutes',
        'status',
        'launched_at',
    ];
    protected $casts = [
        'launched_at' => 'datetime',
    ];

    /* ================= RELATIONS ================= */
    public function meeting()
    {
        return $this->belongsTo(Meeting::class);
    }
    public function questions()
    {
        return $this->hasMany(MeetingPostTestQuestion::class, 'post_test_id')
            ->orderBy('order');
    }
    public function attempts()
    {
        return $this->hasMany(MeetingPostTestAttempt::class, 'post_test_id');
    }
    /* ================= HELPERS ================= */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }
    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }
}

