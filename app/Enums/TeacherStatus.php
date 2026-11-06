<?php

namespace App\Enums;

enum TeacherStatus: string
{
    case Aktif = 'aktif';
    case Nonaktif = 'nonaktif';

    public function label(): string
    {
        return match ($this) {
            self::Aktif => 'Aktif',
            self::Nonaktif => 'Nonaktif',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
