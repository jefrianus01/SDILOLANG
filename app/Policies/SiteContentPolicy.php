<?php

namespace App\Policies;

use App\Models\Gallery;
use App\Models\News;
use App\Models\User;

class SiteContentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isKepalaSekolah() || $user->isSuperAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isKepalaSekolah() || $user->isSuperAdmin();
    }

    public function update(User $user, News|Gallery $model): bool
    {
        return $user->isKepalaSekolah() || $user->isSuperAdmin();
    }

    public function delete(User $user, News|Gallery $model): bool
    {
        return $user->isKepalaSekolah() || $user->isSuperAdmin();
    }
}
