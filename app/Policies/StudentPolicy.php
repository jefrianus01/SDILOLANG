<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;
use App\Support\Roles;

class StudentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canSeeAllStudents() || $user->isWaliKelas();
    }

    public function view(User $user, Student $student): bool
    {
        if ($user->canSeeAllStudents()) {
            return true;
        }

        return $user->isWaliKelas() && $this->inOwnClass($user, $student->classroom_id);
    }

    public function create(User $user): bool
    {
        // Kepala sekolah & super admin hanya membaca (super admin hanya untuk kenaikan kelas).
        return $user->isWaliKelas() && ! $user->canSeeAllStudents() && $user->homeroomClassrooms()->isNotEmpty();
    }

    public function update(User $user, Student $student): bool
    {
        return $user->isWaliKelas()
            && ! $user->canSeeAllStudents()
            && $this->inOwnClass($user, $student->classroom_id);
    }

    public function delete(User $user, Student $student): bool
    {
        return $this->update($user, $student);
    }

    public function import(User $user): bool
    {
        return $this->create($user);
    }

    public function promote(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    private function inOwnClass(User $user, int $classroomId): bool
    {
        return $user->homeroomClassrooms()->pluck('id')->contains($classroomId);
    }
}
