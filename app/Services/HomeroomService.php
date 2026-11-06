<?php

namespace App\Services;

use App\Models\Classroom;
use App\Models\Teacher;
use App\Support\Roles;
use Illuminate\Support\Facades\DB;

class HomeroomService
{
    /**
     * Tunjuk/ganti wali kelas lalu sinkronkan role wali_kelas.
     */
    public function assign(Classroom $classroom, ?Teacher $teacher): void
    {
        DB::transaction(function () use ($classroom, $teacher) {
            $previousTeacherId = $classroom->wali_kelas_id;

            $classroom->update(['wali_kelas_id' => $teacher?->id]);

            if ($previousTeacherId) {
                $previous = Teacher::withTrashed()->find($previousTeacherId);
                if ($previous) {
                    $this->syncRole($previous);
                }
            }

            if ($teacher) {
                $this->syncRole($teacher);
            }
        });
    }

    /**
     * Guru mendapat role wali_kelas hanya jika masih mengampu kelas pada tahun ajaran aktif.
     */
    public function syncRole(Teacher $teacher): void
    {
        $user = $teacher->user;

        if (! $user) {
            return;
        }

        $isHomeroom = Classroom::query()
            ->where('wali_kelas_id', $teacher->id)
            ->whereHas('academicYear', fn ($query) => $query->where('is_active', true))
            ->exists();

        if ($isHomeroom) {
            if (! $user->hasRole(Roles::WALI_KELAS)) {
                $user->assignRole(Roles::WALI_KELAS);
            }
        } else {
            $user->removeRole(Roles::WALI_KELAS);
        }
    }

    public function syncAll(): void
    {
        Teacher::query()->with('user')->each(fn (Teacher $teacher) => $this->syncRole($teacher));
    }
}
