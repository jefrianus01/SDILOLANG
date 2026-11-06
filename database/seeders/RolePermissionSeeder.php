<?php

namespace Database\Seeders;

use App\Support\Permissions;
use App\Support\Roles;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (Permissions::ALL as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $map = [
            Roles::SUPER_ADMIN => Permissions::ALL,
            Roles::KEPALA_SEKOLAH => [
                Permissions::TEACHERS_VIEW,
                Permissions::TEACHERS_CREATE,
                Permissions::TEACHERS_UPDATE,
                Permissions::TEACHERS_DELETE,
                Permissions::TEACHERS_IMPORT,
                Permissions::TEACHERS_RESET_PASSWORD,
                Permissions::TEACHER_TYPES_MANAGE,
                Permissions::STUDENTS_VIEW,
                Permissions::CLASSROOMS_MANAGE,
                Permissions::ACADEMIC_YEARS_MANAGE,
                Permissions::TEACHER_ATTENDANCE_SELF,
                Permissions::TEACHER_ATTENDANCE_VIEW_ALL,
                Permissions::TEACHER_ATTENDANCE_APPROVE,
                Permissions::STUDENT_ATTENDANCE_VIEW_ALL,
                Permissions::REPORTS_VIEW,
                Permissions::REPORTS_EXPORT,
                Permissions::SITE_MANAGE,
                Permissions::SETTINGS_MANAGE,
                Permissions::AUDIT_VIEW,
            ],
            Roles::WALI_KELAS => [
                Permissions::STUDENTS_VIEW,
                Permissions::STUDENTS_CREATE,
                Permissions::STUDENTS_UPDATE,
                Permissions::STUDENTS_DELETE,
                Permissions::STUDENTS_IMPORT,
                Permissions::STUDENT_ATTENDANCE_SELF,
                Permissions::TEACHER_ATTENDANCE_SELF,
                Permissions::REPORTS_VIEW,
                Permissions::REPORTS_EXPORT,
            ],
            Roles::GURU => [
                Permissions::TEACHER_ATTENDANCE_SELF,
            ],
        ];

        foreach ($map as $roleName => $permissions) {
            $role = Role::findOrCreate($roleName, 'web');
            $role->syncPermissions($permissions);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
