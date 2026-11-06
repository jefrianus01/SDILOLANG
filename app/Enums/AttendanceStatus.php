<?php

namespace App\Enums;

enum AttendanceStatus: string
{
    case Hadir = 'hadir';
    case Sakit = 'sakit';
    case Izin = 'izin';
    case Alpa = 'alpa';

    public function label(): string
    {
        return match ($this) {
            self::Hadir => 'Hadir',
            self::Sakit => 'Sakit',
            self::Izin => 'Izin',
            self::Alpa => 'Alpa',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::Hadir => 'bg-emerald-100 text-emerald-700',
            self::Sakit => 'bg-amber-100 text-amber-700',
            self::Izin => 'bg-sky-100 text-sky-700',
            self::Alpa => 'bg-rose-100 text-rose-700',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
