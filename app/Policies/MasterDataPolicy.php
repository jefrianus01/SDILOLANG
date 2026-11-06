<?php

namespace App\Policies;

use App\Models\AcademicYear;
use App\Models\TeacherType;
use App\Models\User;

class MasterDataPolicy
{
    public function manage(User $user): bool
    {
        return $user->isKepalaSekolah() || $user->isSuperAdmin();
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, AcademicYear|TeacherType $model): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $this->manage($user);
    }

    public function update(User $user, AcademicYear|TeacherType $model): bool
    {
        return $this->manage($user);
    }

    public function delete(User $user, AcademicYear|TeacherType $model): bool
    {
        return $this->manage($user);
    }
}
