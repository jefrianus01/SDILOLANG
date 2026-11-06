<?php

namespace Tests\Concerns;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\TeacherType;
use App\Models\User;
use App\Support\Roles;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

trait CreatesSchoolData
{
    protected function seedRoles(): void
    {
        $this->seed(RolePermissionSeeder::class);
    }

    protected function userWithRole(string $role, array $attributes = []): User
    {
        $user = User::factory()->create($attributes);
        $user->assignRole($role);

        return $user;
    }

    /**
     * @return array{0: User, 1: Teacher}
     */
    protected function teacherUser(string $role = Roles::GURU, array $attributes = []): array
    {
        $user = User::factory()->create($attributes);
        $user->assignRole($role);

        $teacher = Teacher::factory()->create([
            'user_id' => $user->id,
            'nama' => $user->name,
            'nip_nik' => $user->username,
        ]);

        return [$user, $teacher];
    }

    protected function activeYear(): AcademicYear
    {
        return AcademicYear::factory()->active()->create();
    }

    protected function classroomFor(Teacher $homeroom, ?AcademicYear $year = null): Classroom
    {
        return Classroom::factory()->create([
            'academic_year_id' => ($year ?? $this->activeYear())->id,
            'wali_kelas_id' => $homeroom->id,
        ]);
    }

    protected function studentIn(Classroom $classroom, array $attributes = []): Student
    {
        return Student::factory()->create(array_merge([
            'classroom_id' => $classroom->id,
        ], $attributes));
    }

    protected function teacherType(string $name = 'Guru Kelas'): TeacherType
    {
        return TeacherType::query()->firstOrCreate(
            ['name' => $name],
            ['slug' => \Illuminate\Support\Str::slug($name), 'is_active' => true]
        );
    }

    /**
     * Buat file XLSX asli untuk pengujian import.
     *
     * @param  array<int, array<int, mixed>>  $rows
     */
    protected function makeXlsx(array $rows, string $filename = 'import.xlsx'): UploadedFile
    {
        $spreadsheet = new Spreadsheet;
        $spreadsheet->getActiveSheet()->fromArray($rows, null, 'A1');

        $path = tempnam(sys_get_temp_dir(), 'absensi-xlsx').'.xlsx';
        (new Xlsx($spreadsheet))->save($path);

        return new UploadedFile(
            $path,
            $filename,
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        );
    }
}
