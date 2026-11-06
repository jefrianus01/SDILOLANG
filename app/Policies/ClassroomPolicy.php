<?php

namespace App\Policies;

use App\Models\Classroom;
use App\Models\User;

class ClassroomPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Classroom $classroom): bool
    {
        if ($user->canSeeAllStudents()) {
            return true;
        }

        return $user->isWaliKelas()
            && $user->homeroomClassrooms()->pluck('id')->contains($classroom->id);
    }

    public function create(User $user): bool
    {
        return $user->isKepalaSekolah() || $user->isSuperAdmin();
    }

    public function update(User $user, Classroom $classroom): bool
    {
        return $user->isKepalaSekolah() || $user->isSuperAdmin();
    }

    public function delete(User $user, Classroom $classroom): bool
    {
        return $user->isKepalaSekolah() || $user->isSuperAdmin();
    }

    /**
     * Hanya wali kelas dari kelas tersebut yang boleh mengisi absensi siswa.
     */
    public function recordAttendance(User $user, Classroom $classroom): bool
    {
        return $user->isWaliKelas()
            && ! $user->canSeeAllStudents()
            && $user->homeroomClassrooms()->pluck('id')->contains($classroom->id);
    }

    public function assignHomeroom(User $user): bool
    {
        return $user->isKepalaSekolah() || $user->isSuperAdmin();
    }
}
