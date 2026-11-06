<?php

namespace App\Services;

use App\Enums\TeacherStatus;
use App\Models\Classroom;
use App\Models\Teacher;
use App\Models\User;
use App\Support\Roles;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TeacherService
{
    /**
     * Buat guru + akun login. Password acak dikembalikan untuk ditampilkan sekali.
     *
     * @param  array{nama: string, nip_nik: string, teacher_type_id: int, status?: string}  $data
     * @return array{0: Teacher, 1: string}
     */
    public function create(array $data): array
    {
        $password = $this->generatePassword();
        $status = $data['status'] ?? TeacherStatus::Aktif->value;

        $teacher = DB::transaction(function () use ($data, $password, $status) {
            $user = User::create([
                'name' => $data['nama'],
                'username' => $data['nip_nik'],
                'password' => $password,
                'must_change_password' => true,
                'is_active' => $status === TeacherStatus::Aktif->value,
            ]);

            $user->assignRole(Roles::GURU);

            return Teacher::create([
                'user_id' => $user->id,
                'nama' => $data['nama'],
                'nip_nik' => $data['nip_nik'],
                'teacher_type_id' => $data['teacher_type_id'],
                'status' => $status,
            ]);
        });

        AuditLogger::log('teacher.created', $teacher, 'Menambah guru: '.$teacher->nama);

        return [$teacher, $password];
    }

    /**
     * @param  array{nama: string, nip_nik: string, teacher_type_id: int, status: string}  $data
     */
    public function update(Teacher $teacher, array $data): Teacher
    {
        DB::transaction(function () use ($teacher, $data) {
            $teacher->update([
                'nama' => $data['nama'],
                'nip_nik' => $data['nip_nik'],
                'teacher_type_id' => $data['teacher_type_id'],
                'status' => $data['status'],
            ]);

            $teacher->user?->update([
                'name' => $data['nama'],
                'username' => $data['nip_nik'],
                'is_active' => $data['status'] === TeacherStatus::Aktif->value,
            ]);
        });

        AuditLogger::log('teacher.updated', $teacher, 'Mengubah guru: '.$teacher->nama);

        return $teacher->refresh();
    }

    /**
     * Reset password ke password acak baru (ditampilkan sekali).
     */
    public function resetPassword(Teacher $teacher): string
    {
        $password = $this->generatePassword();

        $teacher->user?->update([
            'password' => $password,
            'must_change_password' => true,
        ]);

        AuditLogger::log('teacher.password-reset', $teacher, 'Reset password guru: '.$teacher->nama);

        return $password;
    }

    /**
     * Soft delete guru, nonaktifkan akun, dan lepaskan jabatan wali kelas.
     */
    public function delete(Teacher $teacher): void
    {
        DB::transaction(function () use ($teacher) {
            Classroom::query()
                ->where('wali_kelas_id', $teacher->id)
                ->update(['wali_kelas_id' => null]);

            $user = $teacher->user;

            if ($user) {
                $user->removeRole(Roles::WALI_KELAS);
                $user->update(['is_active' => false]);
            }

            $teacher->delete();
        });

        AuditLogger::log('teacher.deleted', $teacher, 'Menghapus guru: '.$teacher->nama);
    }

    public function generatePassword(): string
    {
        return Str::password(10, symbols: false, spaces: false);
    }
}
