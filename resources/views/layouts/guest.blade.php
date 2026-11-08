<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0B2E3A">
    <title>Masuk — {{ \App\Support\SchoolSetting::name() }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800|cormorant-garamond:500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-ivory font-sans text-ink antialiased">
    @php
        $logo = \App\Models\Setting::get(\App\Support\SchoolSetting::LOGO);
        $initials = \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr(\App\Support\SchoolSetting::name(), 0, 2));
    @endphp

    <div class="min-h-screen lg:grid lg:grid-cols-[1.05fr_1fr]">
        <aside class="relative hidden overflow-hidden bg-gradient-to-br from-brand-900 via-ink to-brand-950 text-white lg:flex lg:flex-col lg:justify-between">
            <div class="hero-lines absolute inset-0 opacity-70" aria-hidden="true"></div>
            <div class="absolute -left-24 bottom-0 h-80 w-80 rounded-full bg-gold/10 blur-3xl" aria-hidden="true"></div>

            <div class="relative flex items-center gap-3 px-12 pt-10">
                @if ($logo)
                    <img src="{{ Storage::disk('public')->url($logo) }}" alt="Logo sekolah" class="h-10 w-10 rounded-lg object-contain ring-1 ring-white/20">
                @else
                    <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-white/10 text-sm font-bold text-gold ring-1 ring-white/15">
                        {{ $initials }}
                    </span>
                @endif
                <div>
                    <p class="text-[13px] font-semibold leading-tight">{{ \App\Support\SchoolSetting::name() }}</p>
                    <p class="text-2xs uppercase tracking-[0.16em] text-white/50">Portal Akademik</p>
                </div>
            </div>

            <div class="relative max-w-lg px-12 py-16">
                <p class="flex items-center gap-3 text-2xs font-semibold uppercase tracking-[0.24em] text-gold">
                    <span class="h-px w-8 bg-gold/60"></span>
                    Sistem Absensi Terpadu
                </p>

                <h1 class="mt-6 font-display text-[44px] font-semibold leading-[1.08] tracking-tight text-white">
                    Kehadiran tertata,<br>administrasi lebih tenang.
                </h1>

                <p class="mt-5 max-w-md text-[15px] leading-7 text-white/70">
                    Absensi guru berbasis lokasi dan absensi siswa oleh wali kelas, lengkap dengan rekap bulanan
                    yang siap dicetak.
                </p>

                <dl class="mt-10 space-y-5">
                    <div class="flex items-start gap-3.5">
                        <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white/10 text-gold ring-1 ring-white/10">
                            <x-ui.icon name="map-pin" class="h-4 w-4" />
                        </span>
                        <div>
                            <dt class="text-[13px] font-semibold text-white">Absensi sesuai lokasi</dt>
                            <dd class="mt-0.5 text-[13px] leading-5 text-white/60">Guru hanya dapat absen di dalam radius area sekolah.</dd>
                        </div>
                    </div>

                    <div class="flex items-start gap-3.5">
                        <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white/10 text-gold ring-1 ring-white/10">
                            <x-ui.icon name="chart" class="h-4 w-4" />
                        </span>
                        <div>
                            <dt class="text-[13px] font-semibold text-white">Rekap otomatis</dt>
                            <dd class="mt-0.5 text-[13px] leading-5 text-white/60">Rekap harian dan bulanan dalam bentuk Excel maupun PDF.</dd>
                        </div>
                    </div>

                    <div class="flex items-start gap-3.5">
                        <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white/10 text-gold ring-1 ring-white/10">
                            <x-ui.icon name="shield-check" class="h-4 w-4" />
                        </span>
                        <div>
                            <dt class="text-[13px] font-semibold text-white">Hak akses per peran</dt>
                            <dd class="mt-0.5 text-[13px] leading-5 text-white/60">Kepala sekolah, wali kelas, dan guru memiliki kewenangan masing-masing.</dd>
                        </div>
                    </div>
                </dl>
            </div>

            <div class="relative border-t border-white/10 px-12 py-5">
                <p class="text-xs text-white/40">&copy; {{ date('Y') }} {{ \App\Support\SchoolSetting::name() }}</p>
            </div>
        </aside>

        <main class="flex min-h-screen items-center justify-center px-5 py-10 sm:px-8">
            <div class="w-full max-w-[400px]">
                <div class="mb-8 flex items-center gap-3 lg:hidden">
                    @if ($logo)
                        <img src="{{ Storage::disk('public')->url($logo) }}" alt="Logo sekolah" class="h-10 w-10 rounded-lg object-contain ring-1 ring-gray-200">
                    @else
                        <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-brand-900 text-sm font-bold text-gold">{{ $initials }}</span>
                    @endif
                    <div>
                        <p class="text-sm font-semibold leading-tight text-gray-900">{{ \App\Support\SchoolSetting::name() }}</p>
                        <p class="text-2xs uppercase tracking-[0.16em] text-gray-400">Portal Akademik</p>
                    </div>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-7 shadow-card sm:p-8">
                    {{ $slot }}
                </div>

                <p class="mt-6 text-center text-xs text-gray-500">
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 font-medium text-gray-600 transition-colors hover:text-brand-700">
                        <x-ui.icon name="arrow-right" class="h-3.5 w-3.5 rotate-180" />
                        Kembali ke website sekolah
                    </a>
                </p>
            </div>
        </main>
    </div>
</body>
</html>
