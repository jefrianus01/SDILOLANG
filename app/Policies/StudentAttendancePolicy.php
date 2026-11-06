<?php

namespace App\Policies;

use App\Models\Classroom;
use App\Models\StudentAttendance;
use App\Models\User;

class StudentAttendancePolicy
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

    public function record(User $user, Classroom $classroom): bool
    {
        return $user->isWaliKelas()
            && ! $user->canSeeAllStudents()
            && $user->homeroomClassrooms()->pluck('id')->contains($classroom->id);
    }

    public function view(User $user, StudentAttendance $attendance): bool
    {
        return $this->viewClass($user, $attendance->classroom);
    }
}
