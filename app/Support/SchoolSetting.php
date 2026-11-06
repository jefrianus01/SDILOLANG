<?php

namespace App\Support;

use App\Models\Setting;

final class SchoolSetting
{
    public const NAME = 'school_name';

    public const NPSN = 'school_npsn';

    public const ADDRESS = 'school_address';

    public const PHONE = 'school_phone';

    public const EMAIL = 'school_email';

    public const WEBSITE = 'school_website';

    public const HEADMASTER = 'school_headmaster';

    public const DESCRIPTION = 'school_description';

    public const VISION = 'school_vision';

    public const MISSION = 'school_mission';

    public const LOGO = 'school_logo';

    public const LATITUDE = 'school_latitude';

    public const LONGITUDE = 'school_longitude';

    public const GEOFENCE_RADIUS = 'geofence_radius';

    public const CHECK_IN_TIME = 'check_in_time';

    public const LATE_AFTER = 'late_after';

    public const CHECK_OUT_TIME = 'check_out_time';

    public static function name(): string
    {
        return (string) Setting::get(self::NAME, config('app.name', 'Absensi Sekolah'));
    }

    public static function address(): string
    {
        return (string) Setting::get(self::ADDRESS, '');
    }

    public static function latitude(): ?float
    {
        $value = Setting::get(self::LATITUDE);

        return $value === null || $value === '' ? null : (float) $value;
    }

    public static function longitude(): ?float
    {
        $value = Setting::get(self::LONGITUDE);

        return $value === null || $value === '' ? null : (float) $value;
    }

    public static function radius(): int
    {
        return max(10, Setting::int(self::GEOFENCE_RADIUS, 50));
    }

    public static function lateAfter(): string
    {
        return (string) Setting::get(self::LATE_AFTER, '07:15');
    }

    public static function hasCoordinates(): bool
    {
        return Geo::isValidCoordinate(self::latitude(), self::longitude());
    }
}
