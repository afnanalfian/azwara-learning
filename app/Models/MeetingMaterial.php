<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MeetingMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'meeting_id',
        'file_path',
        'original_name',
    ];

    public function meeting()
    {
        return $this->belongsTo(Meeting::class);
    }
}
