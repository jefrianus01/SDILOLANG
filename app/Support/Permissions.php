<?php

namespace App\Support;

final class Permissions
{
    public const TEACHERS_VIEW = 'teachers.view';

    public const TEACHERS_CREATE = 'teachers.create';

    public const TEACHERS_UPDATE = 'teachers.update';

    public const TEACHERS_DELETE = 'teachers.delete';

    public const TEACHERS_IMPORT = 'teachers.import';

    public const TEACHERS_RESET_PASSWORD = 'teachers.reset-password';

    public const TEACHER_TYPES_MANAGE = 'teacher-types.manage';

    public const STUDENTS_VIEW = 'students.view';

    public const STUDENTS_CREATE = 'students.create';

    public const STUDENTS_UPDATE = 'students.update';

    public const STUDENTS_DELETE = 'students.delete';

    public const STUDENTS_IMPORT = 'students.import';

    public const STUDENTS_PROMOTE = 'students.promote';

    public const CLASSROOMS_MANAGE = 'classrooms.manage';

    public const ACADEMIC_YEARS_MANAGE = 'academic-years.manage';

    public const TEACHER_ATTENDANCE_SELF = 'teacher-attendance.self';

    public const TEACHER_ATTENDANCE_VIEW_ALL = 'teacher-attendance.view-all';

    public const TEACHER_ATTENDANCE_APPROVE = 'teacher-attendance.approve';

    public const STUDENT_ATTENDANCE_SELF = 'student-attendance.self';

    public const STUDENT_ATTENDANCE_VIEW_ALL = 'student-attendance.view-all';

    public const REPORTS_VIEW = 'reports.view';

    public const REPORTS_EXPORT = 'reports.export';

    public const SITE_MANAGE = 'site.manage';

    public const SETTINGS_MANAGE = 'settings.manage';

    public const USERS_MANAGE = 'users.manage';

    public const AUDIT_VIEW = 'audit.view';

    public const ALL = [
        self::TEACHERS_VIEW,
        self::TEACHERS_CREATE,
        self::TEACHERS_UPDATE,
        self::TEACHERS_DELETE,
        self::TEACHERS_IMPORT,
        self::TEACHERS_RESET_PASSWORD,
        self::TEACHER_TYPES_MANAGE,
        self::STUDENTS_VIEW,
        self::STUDENTS_CREATE,
        self::STUDENTS_UPDATE,
        self::STUDENTS_DELETE,
        self::STUDENTS_IMPORT,
        self::STUDENTS_PROMOTE,
        self::CLASSROOMS_MANAGE,
        self::ACADEMIC_YEARS_MANAGE,
        self::TEACHER_ATTENDANCE_SELF,
        self::TEACHER_ATTENDANCE_VIEW_ALL,
        self::TEACHER_ATTENDANCE_APPROVE,
        self::STUDENT_ATTENDANCE_SELF,
        self::STUDENT_ATTENDANCE_VIEW_ALL,
        self::REPORTS_VIEW,
        self::REPORTS_EXPORT,
        self::SITE_MANAGE,
        self::SETTINGS_MANAGE,
        self::USERS_MANAGE,
        self::AUDIT_VIEW,
    ];
}
