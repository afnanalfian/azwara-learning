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
    public function postTest()
    {
        return $this->hasOne(MeetingPostTest::class);
    }
    public function attendances()
    {
        return $this->hasMany(MeetingAttendance::class);
    }
    public function video()
    {
        return $this->hasOne(MeetingVideo::class);
    }

    /* ================= HELPERS ================= */

    public function isUpcoming(): bool
    {
        return $this->status === 'upcoming';
    }

    public function isLive(): bool
    {
        return $this->status === 'live';
    }

    public function isDone(): bool
    {
        return $this->status === 'done';
    }
}
