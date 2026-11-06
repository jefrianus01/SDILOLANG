<?php

namespace App\Policies;

use App\Models\Teacher;
use App\Models\User;

class TeacherPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isKepalaSekolah() || $user->isSuperAdmin();
    }

    public function view(User $user, Teacher $teacher): bool
    {
        return $user->isKepalaSekolah()
            || $user->isSuperAdmin()
            || $teacher->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isKepalaSekolah() || $user->isSuperAdmin();
    }

    public function update(User $user, Teacher $teacher): bool
    {
        return $user->isKepalaSekolah() || $user->isSuperAdmin();
    }

    public function delete(User $user, Teacher $teacher): bool
    {
        return $user->isKepalaSekolah() || $user->isSuperAdmin();
    }

    public function import(User $user): bool
    {
        return $user->isKepalaSekolah() || $user->isSuperAdmin();
    }

    public function resetPassword(User $user, Teacher $teacher): bool
    {
        return $user->isKepalaSekolah() || $user->isSuperAdmin();
    }

    public function assignHomeroom(User $user): bool
    {
        return $user->isKepalaSekolah() || $user->isSuperAdmin();
    }
}
