<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MeetingVideo extends Model
{
    use HasFactory;

    protected $fillable = [
        'meeting_id',
        'bunny_video_id',
        'playback_url',
        'library_id',
        'status',
    ];

    public function meeting()
    {
        return $this->belongsTo(Meeting::class);
    }
    public function isReady(): bool
    {
        return $this->status === 'ready';
    }
}
