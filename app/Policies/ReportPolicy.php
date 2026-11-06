<?php

namespace App\Policies;

use App\Models\Classroom;
use App\Models\User;

class ReportPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canSeeAllStudents() || $user->isWaliKelas();
    }

    public function viewClass(User $user, Classroom $classroom): bool
    {
        if ($user->canSeeAllStudents()) {
            return true;
        }

        return $user->isWaliKelas()
            && $user->homeroomClassrooms()->pluck('id')->contains($classroom->id);
    }

    public function viewTeacherRecap(User $user): bool
    {
        return $user->isKepalaSekolah() || $user->isSuperAdmin();
    }

    public function export(User $user): bool
    {
        return $this->viewAny($user);
    }
}
