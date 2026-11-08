<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Support\Roles;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;
use Symfony\Component\Console\Formatter\OutputFormatter;

class CreateAdmin extends Command
{
    protected $signature = 'admin:create
        {username=admin : Username untuk login}
        {--name= : Nama lengkap admin (default: Administrator)}
        {--password= : Password awal (kosongkan untuk dibuat acak)}
        {--force : Timpa akun dengan username yang sama}';

    protected $description = 'Buat akun Super Admin baru (password wajib diganti saat login pertama)';

    public function handle(): int
    {
        if (! Role::query()->where('name', Roles::SUPER_ADMIN)->exists()) {
            $this->components->info('Role belum ada, menjalankan RolePermissionSeeder...');
            $this->call('db:seed', [
                '--class' => RolePermissionSeeder::class,
                '--force' => true,
            ]);
        }

        $username = trim((string) $this->argument('username'));

        if ($username === '') {
            $this->components->error('Username tidak boleh kosong.');

            return self::FAILURE;
        }

        $existing = User::query()->where('username', $username)->first();

        if ($existing && ! $this->option('force')) {
            $this->components->error("Username [{$username}] sudah dipakai. Gunakan --force untuk menimpa.");

            return self::FAILURE;
        }

        $name = trim((string) ($this->option('name') ?: $existing?->name ?: 'Administrator'));

        if ($name === '') {
            $this->components->error('Nama tidak boleh kosong.');

            return self::FAILURE;
        }

        $password = (string) ($this->option('password') ?: Str::password(16));

        $validator = Validator::make(
            ['password' => $password],
            ['password' => ['required', 'string', Password::defaults()]],
            ['password.min' => 'Password minimal :min karakter'],
        );

        if ($validator->fails()) {
            $this->components->error($validator->errors()->first('password'));

            return self::FAILURE;
        }

        $user = User::query()->updateOrCreate(
            ['username' => $username],
            [
                'name' => $name,
                'password' => $password,
                'must_change_password' => true,
                'is_active' => true,
            ],
        );

        $user->syncRoles([Roles::SUPER_ADMIN]);

        $this->components->info($existing ? 'Akun Super Admin diperbarui.' : 'Akun Super Admin dibuat.');
        $this->newLine();
        $this->line('  Username : <options=bold>'.OutputFormatter::escape($username).'</>');
        $this->line('  Password : <options=bold>'.OutputFormatter::escape($password).'</>');
        $this->newLine();
        $this->components->warn('Password hanya ditampilkan sekali. Ganti saat login pertama.');

        return self::SUCCESS;
    }
}
