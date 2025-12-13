<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Support\Facades\Storage;

/**
 * @method bool hasRole(string|int|array|\Spatie\Permission\Models\Role|\Illuminate\Support\Collection|\BackedEnum $roles, string|null $guard = null)
 */

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        'province_id',
        'regency_id',
        'is_active',
        'email_verified_at'
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function regency()
    {
        return $this->belongsTo(Regency::class);
    }

    public function purchasedMeetings()
    {
        return $this->hasMany(PurchasedMeeting::class);
    }

    public function tryoutResults()
    {
        return $this->hasMany(TryoutResult::class);
    }

    public function dailyQuizResults()
    {
        return $this->hasMany(DailyQuizResult::class);
    }

    public function postTestResults()
    {
        return $this->hasMany(MeetingPostTestResult::class);
    }
    public function sendEmailVerificationNotification()
    {
        $this->notify(new \App\Notifications\CustomVerifyEmail);
    }
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }
    public function getAvatarUrlAttribute()
    {
        return $this->avatar && Storage::disk('public')->exists($this->avatar)
            ? asset('storage/'.$this->avatar)
            : asset('img/user.png');
    }
}
