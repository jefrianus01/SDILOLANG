<?php

namespace App\Enums;

enum TeacherAttendanceStatus: string
{
    case Hadir = 'hadir';
    case Terlambat = 'terlambat';
    case Izin = 'izin';
    case DinasLuar = 'dinas_luar';
    case Sakit = 'sakit';
    case Alpa = 'alpa';

    public function label(): string
    {
        return match ($this) {
            self::Hadir => 'Hadir',
            self::Terlambat => 'Terlambat',
            self::Izin => 'Izin',
            self::DinasLuar => 'Dinas Luar',
            self::Sakit => 'Sakit',
            self::Alpa => 'Alpa',
        };
    }

    public function isPermission(): bool
    {
        return in_array($this, [self::Izin, self::DinasLuar, self::Sakit], true);
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
