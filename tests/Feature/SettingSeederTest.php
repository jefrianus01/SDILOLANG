<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Support\SchoolSetting;
use Database\Seeders\SettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SettingSeederTest extends TestCase
{
    use RefreshDatabase;

    private const DEFAULT_LOGO = 'school/logo-tut-wuri-handayani.png';

    public function test_it_installs_the_default_logo_when_school_has_none(): void
    {
        Storage::fake('public');

        $this->seed(SettingSeeder::class);

        $this->assertSame(self::DEFAULT_LOGO, Setting::get(SchoolSetting::LOGO));
        Storage::disk('public')->assertExists(self::DEFAULT_LOGO);
        $this->assertSame(
            hash_file('sha256', database_path('seeders/assets/logo-tut-wuri-handayani.png')),
            hash_file('sha256', Storage::disk('public')->path(self::DEFAULT_LOGO)),
        );
    }

    public function test_it_does_not_replace_a_custom_logo(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('school/logo-sekolah.png', 'custom');
        Setting::put(SchoolSetting::LOGO, 'school/logo-sekolah.png', 'string', 'school');

        $this->seed(SettingSeeder::class);

        $this->assertSame('school/logo-sekolah.png', Setting::get(SchoolSetting::LOGO));
        Storage::disk('public')->assertMissing(self::DEFAULT_LOGO);
        $this->assertSame('custom', Storage::disk('public')->get('school/logo-sekolah.png'));
    }

    public function test_it_does_not_overwrite_an_existing_default_logo_file(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put(self::DEFAULT_LOGO, 'marker');

        $this->seed(SettingSeeder::class);

        $this->assertSame('marker', Storage::disk('public')->get(self::DEFAULT_LOGO));
        $this->assertSame(self::DEFAULT_LOGO, Setting::get(SchoolSetting::LOGO));
    }

    public function test_it_repairs_the_default_logo_when_the_file_is_gone(): void
    {
        Storage::fake('public');
        Setting::put(SchoolSetting::LOGO, self::DEFAULT_LOGO, 'string', 'school');

        $this->seed(SettingSeeder::class);

        Storage::disk('public')->assertExists(self::DEFAULT_LOGO);
        $this->assertSame(self::DEFAULT_LOGO, Setting::get(SchoolSetting::LOGO));
    }

    public function test_it_installs_the_logo_when_the_setting_is_an_empty_string(): void
    {
        Storage::fake('public');
        Setting::put(SchoolSetting::LOGO, '', 'string', 'school');

        $this->seed(SettingSeeder::class);

        Storage::disk('public')->assertExists(self::DEFAULT_LOGO);
        $this->assertSame(self::DEFAULT_LOGO, Setting::get(SchoolSetting::LOGO));
    }

    public function test_seeder_is_idempotent(): void
    {
        Storage::fake('public');
        $this->seed(SettingSeeder::class);
        $first = Setting::get(SchoolSetting::LOGO);

        $this->seed(SettingSeeder::class);

        $this->assertSame($first, Setting::get(SchoolSetting::LOGO));
    }

    public function test_public_layout_renders_the_logo(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put(self::DEFAULT_LOGO, 'png');
        Setting::put(SchoolSetting::LOGO, self::DEFAULT_LOGO, 'string', 'school');
        Setting::put(SchoolSetting::NAME, 'SDI LOLANG', 'string', 'school');

        $this->get('/login')
            ->assertOk()
            ->assertSee(self::DEFAULT_LOGO, false);
    }
}
