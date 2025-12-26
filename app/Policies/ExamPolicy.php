<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Exam;

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
        if (! $exam->owner || ! $exam->owner instanceof Meeting) {
            return false;
        }

        $meeting = $exam->owner;

        // 1. Jika beli course
        if ($meeting->course_id && $user->hasCourse($meeting->course_id)) {
            return true;
        }

        // 2. Jika beli meeting satuan
        return $user->hasEntitlement('meeting', $meeting->id);
    }
}

