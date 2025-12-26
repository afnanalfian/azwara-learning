<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Exam;
use App\Models\Meeting;

class ExamPolicy
{
    public function view(User $user, Exam $exam): bool
    {
        if (! $user->hasRole('siswa')) {
            return true;
        }

        return match ($exam->type) {

            // Tryout → global
            'tryout' => $user->hasTryoutAccess(),

            // Quiz harian → global
            'quiz'   => $user->hasQuizAccess(),

            // Post-test → ikut akses meeting
            'post_test' => $this->canAccessPostTest($user, $exam),

            default => false,
        };
    }

    protected function canAccessPostTest(User $user, Exam $exam): bool
    {
        // Safety: hanya berlaku untuk post_test
        if ($exam->type !== 'post_test') {
            return false;
        }

        /**
         * ======================================
         * 1. Resolve meeting dari exam owner
         * ======================================
         */
        $meeting = null;

        // Normal case (morphTo bekerja)
        if ($exam->relationLoaded('owner')) {
            $meeting = $exam->owner;
        } else {
            try {
                $meeting = $exam->owner;
            } catch (\Throwable $e) {
                $meeting = null;
            }
        }

        // Fallback: owner_id ada tapi morph gagal
        if (! $meeting && $exam->owner_id) {
            $meeting = Meeting::find($exam->owner_id);
        }

        // Tetap bukan meeting → tolak
        if (! $meeting instanceof Meeting) {
            return false;
        }

        /**
         * ======================================
         * 2. COURSE PACKAGE → AKSES SEMUA
         * ======================================
         */
        if (
            $meeting->course_id &&
            $user->hasCourse($meeting->course_id)
        ) {
            return true;
        }

        /**
         * ======================================
         * 3. MEETING SATUAN
         * ======================================
         */
        return $user->hasEntitlement('meeting', $meeting->id);
    }
}

