<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Support\SchoolSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class SettingSeeder extends Seeder
{
    private const DEFAULT_LOGO = 'school/logo-tut-wuri-handayani.png';

    public function run(): void
    {
        $settings = [
            SchoolSetting::NAME => 'SDI LOLANG',
            SchoolSetting::NPSN => '40101234',
            SchoolSetting::ADDRESS => 'Jl. Pendidikan No. 1',
            SchoolSetting::PHONE => '(0431) 123456',
            SchoolSetting::EMAIL => 'info@example.com',
            SchoolSetting::WEBSITE => 'https://example.com',
            SchoolSetting::HEADMASTER => 'Kepala Sekolah',
            SchoolSetting::DESCRIPTION => 'SDI LOLANG adalah sekolah dasar yang berkomitmen membentuk generasi berkarakter, cerdas, dan peduli lingkungan.',
            SchoolSetting::VISION => 'Terwujudnya peserta didik yang beriman, berkarakter, berprestasi, dan cinta lingkungan.',
            SchoolSetting::MISSION => "Menyelenggarakan pembelajaran aktif dan menyenangkan.\nMengembangkan karakter religius dan disiplin.\nMeningkatkan kompetensi guru dan tenaga kependidikan.\nMembangun budaya peduli lingkungan.",
            SchoolSetting::LATITUDE => '1.4748000',
            SchoolSetting::LONGITUDE => '124.8421000',
            SchoolSetting::GEOFENCE_RADIUS => '50',
            SchoolSetting::CHECK_IN_TIME => '06:30',
            SchoolSetting::LATE_AFTER => '07:15',
            SchoolSetting::CHECK_OUT_TIME => '14:00',
        ];

        $logo = $this->defaultLogo();

        if ($logo !== null) {
            $settings[SchoolSetting::LOGO] = $logo;
        }

        Setting::putMany($settings, 'school');
    }

    /**
     * Pasang logo bawaan (Tut Wuri Handayani) hanya jika sekolah belum punya logo.
     *
     * Logo bawaan juga dipasang ulang bila nilai `school_logo` menunjuk ke logo
     * bawaan tetapi berkasnya hilang (mis. storage dibersihkan saat deploy).
     */
    private function defaultLogo(): ?string
    {
        $current = Setting::get(SchoolSetting::LOGO);

        if ($current !== null && $current !== '' && ($current !== self::DEFAULT_LOGO || Storage::disk('public')->exists(self::DEFAULT_LOGO))) {
            return null;
        }

        if (! Storage::disk('public')->exists(self::DEFAULT_LOGO)) {
            $source = database_path('seeders/assets/logo-tut-wuri-handayani.png');
            $contents = is_file($source) ? @file_get_contents($source) : false;

            if ($contents === false || $contents === '') {
                $this->command?->warn("Logo bawaan tidak dipasang: berkas sumber {$source} tidak ada atau tidak terbaca.");

                return null;
            }

            if (! Storage::disk('public')->put(self::DEFAULT_LOGO, $contents)) {
                $this->command?->warn('Logo bawaan gagal disalin ke disk "public".');

                return null;
            }
        }

        return self::DEFAULT_LOGO;
    }
}
