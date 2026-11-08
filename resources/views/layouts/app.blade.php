<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0B2E3A">
    <title>{{ $title ? $title.' — ' : '' }}{{ \App\Support\SchoolSetting::name() }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-ivory font-sans text-ink antialiased" x-data="{ sidebarOpen: false }" @keydown.escape.window="sidebarOpen = false">
    @php
        $user = auth()->user();
        $teacher = $user?->teacher;
        $homeroom = $user?->isWaliKelas() ? $user->homeroomClassroom() : null;
        $academicYear = \App\Models\AcademicYear::current();
        $logo = \App\Models\Setting::get(\App\Support\SchoolSetting::LOGO);
        $initials = \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr(\App\Support\SchoolSetting::name(), 0, 2));
    @endphp

    <a href="#main-content"
       class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded-lg focus:bg-brand-900 focus:px-4 focus:py-2 focus:text-sm focus:font-semibold focus:text-white">
        Lewati ke konten
    </a>

    <div class="lg:flex">
        {{-- Overlay untuk drawer di layar kecil --}}
        <div x-show="sidebarOpen" x-cloak x-transition.opacity @click="sidebarOpen = false"
             class="fixed inset-0 z-30 bg-ink/40 backdrop-blur-[2px] lg:hidden"></div>

        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
               class="fixed inset-y-0 left-0 z-40 flex w-[264px] flex-col border-r border-gray-200 bg-white transition-transform duration-200 ease-out lg:sticky lg:top-0 lg:h-screen lg:translate-x-0">

            <div class="flex h-16 shrink-0 items-center gap-3 border-b border-gray-100 bg-gradient-to-r from-brand-50/80 to-white px-5">
                @if ($logo)
                    <img src="{{ Storage::disk('public')->url($logo) }}" alt="Logo {{ \App\Support\SchoolSetting::name() }}"
                         class="h-9 w-9 shrink-0 rounded-lg object-contain ring-1 ring-gray-200">
                @else
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-brand-900 text-[13px] font-bold tracking-wide text-gold">
                        {{ $initials }}
                    </span>
                @endif

                <div class="min-w-0">
                    <p class="truncate text-[13px] font-semibold leading-tight text-gray-900">{{ \App\Support\SchoolSetting::name() }}</p>
                    <p class="mt-0.5 text-2xs font-medium uppercase tracking-[0.12em] text-gray-400">Sistem Absensi</p>
                </div>

                <button type="button" @click="sidebarOpen = false"
                        class="ml-auto rounded-lg p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-600 lg:hidden"
                        aria-label="Tutup menu">
                    <x-ui.icon name="x" class="h-4 w-4" />
                </button>
            </div>

            <nav class="flex-1 space-y-5 overflow-y-auto px-3 py-4" aria-label="Navigasi utama">
                <div>
                    <x-ui.nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        <x-slot name="icon"><x-ui.icon name="dashboard" /></x-slot>
                        Dashboard
                    </x-ui.nav-link>
                </div>

                <div>
                    <p class="nav-section">Absensi</p>
                    <div class="space-y-0.5">
                        @if ($teacher)
                            <x-ui.nav-link :href="route('teacher-attendance.index')" :active="request()->routeIs('teacher-attendance.index')">
                                <x-slot name="icon"><x-ui.icon name="calendar-check" /></x-slot>
                                Absensi Saya
                            </x-ui.nav-link>
                        @endif
                        @can('viewAny', App\Models\StudentAttendance::class)
                            <x-ui.nav-link :href="route('student-attendance.index')" :active="request()->routeIs('student-attendance.*')">
                                <x-slot name="icon"><x-ui.icon name="clipboard" /></x-slot>
                                Absensi Siswa
                            </x-ui.nav-link>
                        @endcan
                        @can('viewAny', App\Models\TeacherAttendance::class)
                            <x-ui.nav-link :href="route('teacher-attendance.admin')" :active="request()->routeIs('teacher-attendance.admin')">
                                <x-slot name="icon"><x-ui.icon name="user-check" /></x-slot>
                                Absensi Guru
                            </x-ui.nav-link>
                        @endcan
                        @can('approve', App\Models\TeacherAttendance::class)
                            <x-ui.nav-link :href="route('teacher-attendance.approvals')" :active="request()->routeIs('teacher-attendance.approvals')">
                                <x-slot name="icon"><x-ui.icon name="shield-check" /></x-slot>
                                Persetujuan Izin
                            </x-ui.nav-link>
                        @endcan
                    </div>
                </div>

                @if ($user?->isKepalaSekolah() || $user?->isSuperAdmin() || $user?->isWaliKelas())
                    <div>
                        <p class="nav-section">Data</p>
                        <div class="space-y-0.5">
                            @can('viewAny', App\Models\Teacher::class)
                                <x-ui.nav-link :href="route('teachers.index')" :active="request()->routeIs('teachers.*')">
                                    <x-slot name="icon"><x-ui.icon name="users" /></x-slot>
                                    Data Guru
                                </x-ui.nav-link>
                            @endcan
                            @can('manage', App\Models\TeacherType::class)
                                <x-ui.nav-link :href="route('teacher-types.index')" :active="request()->routeIs('teacher-types.*')">
                                    <x-slot name="icon"><x-ui.icon name="tag" /></x-slot>
                                    Jenis Guru
                                </x-ui.nav-link>
                            @endcan
                            @can('manage', App\Models\AcademicYear::class)
                                <x-ui.nav-link :href="route('academic-years.index')" :active="request()->routeIs('academic-years.*')">
                                    <x-slot name="icon"><x-ui.icon name="calendar" /></x-slot>
                                    Tahun Ajaran
                                </x-ui.nav-link>
                            @endcan
                            <x-ui.nav-link :href="route('classrooms.index')" :active="request()->routeIs('classrooms.*')">
                                <x-slot name="icon"><x-ui.icon name="building" /></x-slot>
                                Kelas 1–6
                            </x-ui.nav-link>
                            @can('viewAny', App\Models\Student::class)
                                <x-ui.nav-link :href="route('students.index')" :active="request()->routeIs('students.*')">
                                    <x-slot name="icon"><x-ui.icon name="graduation-cap" /></x-slot>
                                    Data Siswa
                                </x-ui.nav-link>
                            @endcan
                        </div>
                    </div>
                @endif

                @if ($user?->canSeeAllStudents() || $homeroom)
                    <div>
                        <p class="nav-section">Laporan</p>
                        <div class="space-y-0.5">
                            @if ($homeroom)
                                <x-ui.nav-link :href="route('reports.class', $homeroom)" :active="request()->routeIs('reports.class')">
                                    <x-slot name="icon"><x-ui.icon name="chart" /></x-slot>
                                    Rekap Kelas Saya
                                </x-ui.nav-link>
                            @elseif ($user?->canSeeAllStudents())
                                <x-ui.nav-link :href="route('classrooms.index')" :active="request()->routeIs('reports.class')">
                                    <x-slot name="icon"><x-ui.icon name="chart" /></x-slot>
                                    Rekap per Kelas
                                </x-ui.nav-link>
                            @endif
                            @can('viewAny', App\Models\TeacherAttendance::class)
                                <x-ui.nav-link :href="route('reports.teacher')" :active="request()->routeIs('reports.teacher')">
                                    <x-slot name="icon"><x-ui.icon name="chart" /></x-slot>
                                    Rekap Guru
                                </x-ui.nav-link>
                            @endcan
                        </div>
                    </div>
                @endif

                @can('viewAny', App\Models\News::class)
                    <div>
                        <p class="nav-section">Website</p>
                        <div class="space-y-0.5">
                            <x-ui.nav-link :href="route('admin.news.index')" :active="request()->routeIs('admin.news.*')">
                                <x-slot name="icon"><x-ui.icon name="newspaper" /></x-slot>
                                Berita
                            </x-ui.nav-link>
                            <x-ui.nav-link :href="route('admin.gallery.index')" :active="request()->routeIs('admin.gallery.*')">
                                <x-slot name="icon"><x-ui.icon name="photo" /></x-slot>
                                Galeri
                            </x-ui.nav-link>
                        </div>
                    </div>
                @endcan

                @if ($user?->isKepalaSekolah() || $user?->isSuperAdmin())
                    <div>
                        <p class="nav-section">Sistem</p>
                        <div class="space-y-0.5">
                            @can('manage', App\Models\Setting::class)
                                <x-ui.nav-link :href="route('settings.edit')" :active="request()->routeIs('settings.*')">
                                    <x-slot name="icon"><x-ui.icon name="settings" /></x-slot>
                                    Pengaturan Sekolah
                                </x-ui.nav-link>
                            @endcan
                            @can('viewAny', App\Models\User::class)
                                <x-ui.nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                                    <x-slot name="icon"><x-ui.icon name="user" /></x-slot>
                                    Pengguna
                                </x-ui.nav-link>
                            @endcan
                            @can('viewAny', App\Models\AuditLog::class)
                                <x-ui.nav-link :href="route('audit.index')" :active="request()->routeIs('audit.*')">
                                    <x-slot name="icon"><x-ui.icon name="activity" /></x-slot>
                                    Log Aktivitas
                                </x-ui.nav-link>
                            @endcan
                        </div>
                    </div>
                @endif
            </nav>

            <div class="shrink-0 space-y-3 border-t border-gray-100 px-4 py-4">
                <div class="rounded-lg border border-gray-200 bg-gray-50/60 px-3 py-2.5">
                    <p class="text-2xs font-semibold uppercase tracking-[0.12em] text-gray-400">Tahun ajaran</p>
                    <p class="mt-1 text-[13px] font-semibold text-gray-800">{{ $academicYear?->name ?? 'Belum diatur' }}</p>
                </div>

                <a href="{{ route('home') }}" target="_blank" rel="noopener"
                   class="group flex items-center justify-between gap-2 rounded-lg px-1 text-xs font-medium text-gray-500 transition-colors hover:text-brand-700">
                    Lihat website sekolah
                    <x-ui.icon name="external" class="h-3.5 w-3.5 text-gray-400 transition-colors group-hover:text-brand-600" />
                </a>
            </div>
        </aside>

        <div class="flex min-w-0 flex-1 flex-col">
            <header class="sticky top-0 z-20 border-b border-gray-200 bg-white/85 backdrop-blur">
                <div class="pointer-events-none absolute inset-x-0 top-0 h-0.5 bg-gradient-to-r from-brand-500 via-gold to-brand-500" aria-hidden="true"></div>
                <div class="flex h-16 items-center gap-3 px-4 sm:px-6 lg:px-8">
                    <button type="button" @click="sidebarOpen = ! sidebarOpen"
                            class="-ml-1 rounded-lg p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-700 lg:hidden"
                            aria-label="Buka menu" :aria-expanded="sidebarOpen">
                        <x-ui.icon name="menu" class="h-5 w-5" />
                    </button>

                    <div class="min-w-0 flex-1">
                        <nav class="flex items-center gap-1.5 text-2xs font-medium uppercase tracking-[0.12em] text-gray-400" aria-label="Breadcrumb">
                            <span>Sistem Absensi</span>
                            <x-ui.icon name="chevron-right" class="h-3 w-3" />
                            <span class="truncate text-gray-600">{{ $title ?? 'Dashboard' }}</span>
                        </nav>
                    </div>

                    <div class="flex items-center gap-2 sm:gap-3">
                        <span class="hidden items-center gap-1.5 text-xs font-medium text-gray-500 md:inline-flex">
                            <x-ui.icon name="calendar" class="h-3.5 w-3.5 text-gray-400" />
                            {{ now()->translatedFormat('l, d F Y') }}
                        </span>

                        <span class="hidden h-6 w-px bg-gray-200 md:block"></span>

                        <x-dropdown align="right" width="64">
                            <x-slot name="trigger">
                                <button type="button"
                                        class="flex items-center gap-2.5 rounded-full py-1 pl-1 pr-2 text-left transition-colors hover:bg-gray-100 sm:pr-3">
                                    @if ($user?->photo_path)
                                        <img src="{{ Storage::disk('public')->url($user->photo_path) }}"
                                             alt="Foto {{ $user->name }}" class="h-8 w-8 rounded-full object-cover ring-1 ring-gray-200">
                                    @else
                                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-brand-100 text-[11px] font-bold text-brand-800 ring-1 ring-brand-200/60">
                                            {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($user?->name ?? 'U', 0, 2)) }}
                                        </span>
                                    @endif

                                    <span class="hidden min-w-0 sm:block">
                                        <span class="block max-w-[160px] truncate text-[13px] font-semibold leading-tight text-gray-900">{{ $user?->name }}</span>
                                        <span class="block text-2xs text-gray-500">
                                            {{ $user?->getRoleNames()->map(fn ($role) => \App\Support\Roles::label($role))->join(', ') }}
                                        </span>
                                    </span>

                                    <x-ui.icon name="chevron-down" class="h-4 w-4 text-gray-400" />
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <div class="border-b border-gray-100 px-3.5 py-2.5">
                                    <p class="truncate text-[13px] font-semibold text-gray-900">{{ $user?->name }}</p>
                                    <p class="truncate text-xs text-gray-500">{{ $user?->username }}</p>
                                </div>

                                <div class="py-1">
                                    <x-dropdown-link :href="route('profile.edit')">Profil &amp; Password</x-dropdown-link>
                                    <x-dropdown-link :href="route('home')" target="_blank" rel="noopener">Website Sekolah</x-dropdown-link>
                                </div>

                                <div class="border-t border-gray-100 py-1">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <x-dropdown-link :href="route('logout')"
                                                         class="text-rose-600 hover:bg-rose-50 hover:text-rose-700"
                                                         onclick="event.preventDefault(); this.closest('form').submit();">
                                            Keluar
                                        </x-dropdown-link>
                                    </form>
                                </div>
                            </x-slot>
                        </x-dropdown>
                    </div>
                </div>
            </header>

            <main id="main-content" class="mx-auto w-full max-w-[1440px] flex-1 px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
                @if (session('success'))
                    <x-ui.alert type="success" :message="session('success')" class="mb-4" />
                @endif
                @if (session('error'))
                    <x-ui.alert type="error" :message="session('error')" class="mb-4" />
                @endif
                @if (session('warning'))
                    <x-ui.alert type="warning" :message="session('warning')" class="mb-4" />
                @endif
                @if (session('status') === 'profile-updated')
                    <x-ui.alert type="success" message="Profil berhasil diperbarui." class="mb-4" />
                @endif
                @if (session('status') === 'photo-deleted')
                    <x-ui.alert type="success" message="Foto profil berhasil dihapus." class="mb-4" />
                @endif
                @if ($errors->any())
                    <x-ui.alert type="error" class="mb-4">
                        <p class="font-semibold">Periksa kembali data yang diisi:</p>
                        <ul class="mt-1 list-inside list-disc space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </x-ui.alert>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>

    @if (session('generated_account'))
        @php($account = session('generated_account'))
        <div x-data="{ open: true }" x-show="open" x-cloak @keydown.escape.window="open = false"
             role="dialog" aria-modal="true" aria-labelledby="generated-account-title"
             class="fixed inset-0 z-50 flex items-center justify-center bg-ink/50 p-4 backdrop-blur-sm">
            <div class="w-full max-w-lg overflow-hidden rounded-xl border border-gray-200 bg-white shadow-float">
                <div class="flex items-start justify-between gap-4 border-b border-gray-100 px-6 py-5">
                    <div>
                        <h2 id="generated-account-title" class="text-base font-semibold text-gray-900">Akun login dibuat</h2>
                        <p class="mt-1 text-[13px] text-gray-500">Catat sekarang — password ini hanya ditampilkan sekali.</p>
                    </div>
                    <button type="button" @click="open = false" class="rounded-lg p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-600" aria-label="Tutup">
                        <x-ui.icon name="x" class="h-4 w-4" />
                    </button>
                </div>

                <dl class="space-y-2 px-6 py-5 text-sm">
                    <div class="flex items-center justify-between gap-4 rounded-lg bg-gray-50 px-4 py-3">
                        <dt class="text-[13px] text-gray-500">Nama</dt>
                        <dd class="text-[13px] font-semibold text-gray-900">{{ $account['name'] }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-4 rounded-lg bg-gray-50 px-4 py-3">
                        <dt class="text-[13px] text-gray-500">Username</dt>
                        <dd class="font-mono text-[13px] font-semibold text-gray-900">{{ $account['username'] }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-4 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3">
                        <dt class="text-[13px] text-amber-700">Password awal</dt>
                        <dd class="font-mono text-base font-bold text-amber-900">{{ $account['password'] }}</dd>
                    </div>
                </dl>

                <div class="flex items-center justify-between gap-4 border-t border-gray-100 px-6 py-4">
                    <p class="text-xs text-gray-500">Guru wajib mengganti password saat login pertama.</p>
                    <button type="button" @click="open = false" class="btn btn-primary shrink-0">Saya sudah mencatat</button>
                </div>
            </div>
        </div>
    @endif

    <x-ui.confirm-modal />

    @stack('scripts')
</body>
</html>
