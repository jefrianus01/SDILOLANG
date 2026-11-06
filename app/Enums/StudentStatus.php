<?php

namespace App\Enums;

enum StudentStatus: string
{
    case Aktif = 'aktif';
    case Nonaktif = 'nonaktif';
    case Pindah = 'pindah';
    case Lulus = 'lulus';

    public function label(): string
    {
        return match ($this) {
            self::Aktif => 'Aktif',
            self::Nonaktif => 'Nonaktif',
            self::Pindah => 'Pindah',
            self::Lulus => 'Lulus',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
