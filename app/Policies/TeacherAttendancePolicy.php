<?php

namespace App\Policies;

use App\Models\TeacherAttendance;
use App\Models\User;

class TeacherAttendancePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isKepalaSekolah() || $user->isSuperAdmin();
    }

    public function view(User $user, TeacherAttendance $attendance): bool
    {
        if ($user->isKepalaSekolah() || $user->isSuperAdmin()) {
            return true;
        }

        return $attendance->teacher?->user_id === $user->id;
    }

    public function checkIn(User $user): bool
    {
        return $user->teacher !== null && $user->teacher->isAktif();
    }

    public function requestPermission(User $user): bool
    {
        return $this->checkIn($user);
    }

    public function approve(User $user): bool
    {
        return $user->isKepalaSekolah() || $user->isSuperAdmin();
    }
}
