<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exam extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'type',
        'title',
        'exam_date',
        'duration_minutes',
        'status',
        'owner_type',
        'owner_id',
        'created_by',
    ];

    protected $casts = [
        'exam_date' => 'datetime',
    ];

    /* ================= RELATIONS ================= */

    // post test → Meeting
    public function owner(): MorphTo
    {
        return $this->morphTo();
    }

    public function questions(): HasMany
    {
        return $this->hasMany(ExamQuestion::class);
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(ExamAttempt::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi ke Productable
     * Nama diubah dari product() menjadi productable()
     */
    public function productable()
    {
        return $this->morphOne(Productable::class, 'productable')->with('product');
    }

    /* ================= HELPERS ================= */

    /**
     * Accessor untuk mendapatkan Product langsung
     * Tetap menggunakan nama product
     */
    public function getProductAttribute()
    {
        // Cek apakah relasi productable sudah dimuat
        if (!$this->relationLoaded('productable')) {
            $this->load('productable');
        }

        // Kembalikan product dari productable
        return $this->productable?->product;
    }

    /* ================= METHODS ================= */

    public function isPostTest(): bool
    {
        return $this->type === 'post_test';
    }

    public function isQuiz(): bool
    {
        return $this->type === 'quiz';
    }

    public function isTryout(): bool
    {
        return $this->type === 'tryout';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function hasTimeWindow(): bool
    {
        if (!$this->scheduled_at) return true;

        return now()->between(
            $this->scheduled_at,
            $this->scheduled_end_at ?? now()->addYears(10)
        );
    }

    public function getContextTitleAttribute(): string
    {
        // POST TEST (melekat ke meeting)
        if (
            $this->owner_type === \App\Models\Meeting::class &&
            $this->owner
        ) {
            return (string) $this->owner->title;
        }

        // QUIZ / TRYOUT
        if (!empty($this->title)) {
            return (string) $this->title;
        }

        // fallback wajib string
        return 'Exam';
    }

    public function backRoute(): string
    {
        // ================= POST TEST =================
        if (
            $this->type === 'post_test' &&
            $this->owner instanceof Meeting
        ) {
            return route('meeting.show', $this->owner);
        }

        // ================= QUIZ =================
        if ($this->type === 'quiz') {
            return route('quizzes.index');
        }

        // ================= TRYOUT =================
        if ($this->type === 'tryout') {
            return route('tryouts.index');
        }

        // ================= FALLBACK =================
        return route('dashboard.redirect');
    }
}
