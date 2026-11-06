<?php

namespace App\Models\Scopes;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class StudentScope implements Scope
{
    /**
     * Wali kelas hanya boleh mengakses siswa di kelasnya sendiri.
     * Kepala sekolah & super admin melihat seluruh siswa (read-only).
     */
    public function apply(Builder $builder, Model $model): void
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            return;
        }

        if ($user->canSeeAllStudents()) {
            return;
        }

        if ($user->isWaliKelas()) {
            $classroomIds = $user->homeroomClassrooms()->pluck('id');

            if ($classroomIds->isEmpty()) {
                $builder->whereRaw('1 = 0');

                return;
            }

            $builder->whereIn($model->qualifyColumn('classroom_id'), $classroomIds);

            return;
        }

        // Guru tanpa kelas: tidak boleh melihat data siswa sama sekali.
        $builder->whereRaw('1 = 0');
    }
}
