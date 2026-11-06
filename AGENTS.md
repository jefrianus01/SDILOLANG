# AGENTS.md

Laravel 12 school app ("Absensi SDI LOLANG"): Indonesian-language teacher/student attendance, reports/exports, and a public school website (Blade + Tailwind/Alpine). No git repository, no CI, no JS tests — verify by running the app or PHPUnit.

## Commands

- Local dev: `php artisan serve` plus `npm run dev` (Vite). `npm run build` regenerates `public/build`; run it after CSS/JS changes.
- `composer dev` runs serve + database queue listener + Pail + Vite concurrently.
- DB is SQLite locally (`database/database.sqlite`, current `.env`): `php artisan migrate --seed` creates schema + demo data. `migrate:fresh --seed` is destructive.
- Tests: `php artisan test` (PHPUnit). Focus with `php artisan test --filter=SmokeTest` or `php artisan test tests/Feature/ImportTest.php`.
- Formatter: `vendor/bin/pint` (default preset, no `pint.json`). The tree is not Pint-clean — format only files you changed (`vendor/bin/pint app/Foo.php`), not the whole project.
- Docker: `docker compose up --build` → Nginx :8080, MySQL 8, Mailpit :8025. Compose mounts the repo and reads the host `.env` (no `env_file:`); switch that file to the MySQL values shown in `.env.example` if you want the container DB. Vite must still be started separately (compose publishes 5173 but starts no dev server).
- `composer setup` installs, keys, migrates, and builds assets but does **not** seed. On a fresh checkout it copies `.env.example` (MySQL/Docker) when `.env` is absent; keep the existing SQLite `.env` for local work. `composer test` clears config before testing.

## Demo data and auth

- Login uses `username`, not email; there is no self-registration.
- Seeded accounts: `superadmin` / `Super#2026`; teachers use their NIP as username (e.g. `198502032010011002`) with password `Guru#2026`. The local DB is already seeded.
- New/reset teacher accounts get a random password and `must_change_password=true`; middleware `password.changed` allows only profile, password update, and logout until it is changed. Preserve this when creating accounts or tests.
- Seeders use `updateOrCreate` and can be rerun on intact data, but they reset demo passwords and force the demo academic year active. They also fail if demo teachers/students were soft-deleted, because unique `nip_nik`/`nomor_induk_siswa` rows still exist but `updateOrCreate` does not see trashed rows.

## Architecture notes

- UI labels and flash messages are Indonesian, but auth/profile URLs are English (`login`, `password`, `profile`). There is no `lang/` directory, so built-in Laravel validation messages render as raw keys (e.g. `validation.required`). Routes live in `routes/web.php`; auth routes in `routes/auth.php`.
- Authorization is policy-based (`can:` route middleware / `Gate::authorize`), backed by Spatie roles/permissions. Role and permission names are centralized in `app/Support/Roles.php` and `Permissions.php`; run `RolePermissionSeeder` before any role-based test. `AcademicYear`, `TeacherType`, `News`, and `Gallery` policies are explicitly mapped in `AppServiceProvider` — auto-discovery will not find `MasterDataPolicy`/`SiteContentPolicy`.
- `Student` has a global `StudentScope`: super admin and kepala sekolah see all students, wali kelas only the homeroom class of the active academic year, guru none. It applies to every authenticated query, including counts; use `withoutGlobalScope(StudentScope::class)` only deliberately. Route model binding is the exception — `Student::resolveRouteBindingQuery()` deliberately bypasses the scope so policies return 403 instead of 404; never rely on the scope alone for per-record access. Student create/update/import/delete is wali-kelas-only — kepala sekolah and super admin are read-only for students (super admin may promote).
- School settings (name, coordinates, geofence radius, late threshold) live in the `settings` table and are cached forever via `Setting::allCached()`. Raw SQL updates will not invalidate the cache; use `/pengaturan`, `SettingSeeder`, or the `Setting` model. The `SCHOOL_*` variables in `.env.example` are unused.
- Teacher check-in/out goes through `TeacherAttendanceService`: Haversine distance to the school must be inside the configured radius, duplicate check-in/out is rejected, and `late_after` decides "terlambat". Permission requests are approved only by kepala sekolah/super admin. Keep business logic in services and call `AuditLogger::log()` for mutations, as existing controllers do.
- Excel import/export uses maatwebsite/excel 4.0.6; PDF uses dompdf. Import is currently broken because `App\Imports\RawRowsImport` implements only `WithHeadingRow`, while v4's `Excel::toArray()` requires the `Import` contract. XLSX export and PDF generation work.

## Testing

- `phpunit.xml` forces in-memory SQLite, array cache/session/mail, and sync queue; feature tests use `RefreshDatabase`.
- Reuse `Tests\Concerns\CreatesSchoolData` (`teacherUser()`, `classroomFor()`, `studentIn()`, `makeXlsx()`) instead of hand-building fixtures. Seed roles (`seedRoles()`) and, for attendance tests, school settings explicitly.
- Baseline is red as of 2026-10-31 (7–8 failures; one is flaky): Excel v4 import failures; wali-kelas dashboard `TypeError` from `$rows->pluck('total', 'status')` at `resources/views/dashboard.blade.php:154` because `status` is cast to an enum; missing GD makes `UploadedFile::fake()->image()` fail; `/siswa/import` returns 403 for super admin (policy is wali-kelas-only); two `activeYear()` factories in one test can randomly pick the same academic-year name. Check this baseline before attributing failures to your change.

## Gotchas

- `public/storage` is missing; run `php artisan storage:link` or photos, logos, gallery images, and permission attachments will not load (all use the `public` disk).
- Local PHP 8.4 has no GD; the Docker image installs it, so image upload behaviour differs outside Docker.
- Timezone is `Asia/Makassar` and locale `id`; date-sensitive UI and tests assume Indonesian local time.
