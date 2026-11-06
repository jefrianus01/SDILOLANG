<?php

namespace App\Support;

final class Roles
{
    public const SUPER_ADMIN = 'super_admin';

    public const KEPALA_SEKOLAH = 'kepala_sekolah';

    public const WALI_KELAS = 'wali_kelas';

    public const GURU = 'guru';

    public const ALL = [
        self::SUPER_ADMIN,
        self::KEPALA_SEKOLAH,
        self::WALI_KELAS,
        self::GURU,
    ];

    public static function labels(): array
    {
        return [
            self::SUPER_ADMIN => 'Super Admin',
            self::KEPALA_SEKOLAH => 'Kepala Sekolah',
            self::WALI_KELAS => 'Wali Kelas',
            self::GURU => 'Guru',
        ];
    }

    public static function label(string $role): string
    {
        return self::labels()[$role] ?? $role;
    }
}
