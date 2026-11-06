@props(['title' => null])

<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0A1F33">
    <title>{{ $title ? $title.' — ' : '' }}{{ \App\Support\SchoolSetting::name() }}</title>
    <meta name="description" content="{{ \App\Models\Setting::get(\App\Support\SchoolSetting::DESCRIPTION, 'Website resmi '.\App\Support\SchoolSetting::name()) }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800|cormorant-garamond:500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-ivory font-sans text-ink antialiased" x-data="{ open: false }">
    @php
        $logo = \App\Models\Setting::get(\App\Support\SchoolSetting::LOGO);
        $initials = \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr(\App\Support\SchoolSetting::name(), 0, 2));

        $navItems = [
            ['label' => 'Beranda', 'route' => 'home', 'active' => request()->routeIs('home')],
            ['label' => 'Profil', 'route' => 'school.profile', 'active' => request()->routeIs('school.profile')],
            ['label' => 'Berita', 'route' => 'news.index', 'active' => request()->routeIs('news.*')],
            ['label' => 'Galeri', 'route' => 'gallery.index', 'active' => request()->routeIs('gallery.*')],
        ];
    @endphp

    <a href="#main-content"
       class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded-lg focus:bg-ink focus:px-4 focus:py-2 focus:text-sm focus:font-semibold focus:text-white">
        Lewati ke konten
    </a>

    <header class="sticky top-0 z-30 border-b border-gray-200/80 bg-ivory/90 backdrop-blur">
        <div class="mx-auto flex h-[72px] max-w-6xl items-center justify-between gap-6 px-4 sm:px-6">
            <a href="{{ route('home') }}" class="flex min-w-0 items-center gap-3">
                @if ($logo)
                    <img src="{{ Storage::disk('public')->url($logo) }}" alt="Logo {{ \App\Support\SchoolSetting::name() }}"
                         class="h-10 w-10 shrink-0 rounded-lg object-contain ring-1 ring-gray-200">
                @else
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-ink text-[13px] font-bold tracking-wide text-gold">
                        {{ $initials }}
                    </span>
                @endif

                <span class="min-w-0">
                    <span class="block truncate text-[14px] font-semibold leading-tight text-ink">{{ \App\Support\SchoolSetting::name() }}</span>
                    <span class="mt-0.5 block text-2xs uppercase tracking-[0.14em] text-gray-400">Website Resmi Sekolah</span>
                </span>
            </a>

            <nav class="hidden items-stretch self-stretch md:flex" aria-label="Navigasi situs">
                @foreach ($navItems as $item)
                    <a href="{{ route($item['route']) }}"
                       @if ($item['active']) aria-current="page" @endif
                       class="relative flex items-center border-b-2 px-4 text-[13px] font-medium transition-colors
                              {{ $item['active'] ? 'border-gold text-ink' : 'border-transparent text-gray-600 hover:text-ink' }}">
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>

            <div class="flex items-center gap-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-primary btn-sm hidden sm:inline-flex">Buka Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary btn-sm hidden sm:inline-flex">
                        <x-ui.icon name="lock" class="h-3.5 w-3.5" />
                        Portal Absensi
                    </a>
                @endauth

                <button type="button" @click="open = ! open"
                        class="rounded-lg p-2 text-gray-600 hover:bg-gray-100 md:hidden"
                        :aria-expanded="open" aria-label="Menu">
                    <x-ui.icon name="menu" class="h-5 w-5" />
                </button>
            </div>
        </div>

        <div x-cloak x-show="open" x-transition.origin.top class="border-t border-gray-200 bg-ivory px-4 py-3 md:hidden">
            <div class="flex flex-col">
                @foreach ($navItems as $item)
                    <a href="{{ route($item['route']) }}"
                       class="flex items-center justify-between rounded-lg px-3 py-2.5 text-sm font-medium {{ $item['active'] ? 'bg-brand-50 text-brand-900' : 'text-gray-600 hover:bg-gray-100' }}">
                        {{ $item['label'] }}
                        <x-ui.icon name="chevron-right" class="h-4 w-4 text-gray-400" />
                    </a>
                @endforeach

                <div class="mt-3 border-t border-gray-200 pt-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn btn-primary w-full">Buka Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary w-full">Portal Absensi</a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <main id="main-content">{{ $slot }}</main>

    <footer class="mt-24 border-t border-white/10 bg-ink text-white/70">
        <div class="mx-auto grid max-w-6xl gap-10 px-4 py-14 sm:px-6 md:grid-cols-2 lg:grid-cols-4">
            <div class="lg:col-span-1">
                <div class="flex items-center gap-3">
                    @if ($logo)
                        <img src="{{ Storage::disk('public')->url($logo) }}" alt="Logo sekolah" class="h-9 w-9 rounded-lg object-contain ring-1 ring-white/15">
                    @else
                        <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-white/10 text-xs font-bold text-gold ring-1 ring-white/10">
                            {{ $initials }}
                        </span>
                    @endif
                    <p class="text-sm font-semibold leading-tight text-white">{{ \App\Support\SchoolSetting::name() }}</p>
                </div>

                <p class="mt-4 text-[13px] leading-6">{{ \App\Support\SchoolSetting::address() ?: 'Alamat sekolah belum diatur.' }}</p>
                <p class="mt-3 text-2xs uppercase tracking-[0.14em] text-white/40">NPSN {{ \App\Models\Setting::get(\App\Support\SchoolSetting::NPSN, '-') }}</p>
            </div>

            <div>
                <p class="text-2xs font-semibold uppercase tracking-[0.16em] text-white/40">Tautan</p>
                <div class="mt-4 flex flex-col gap-2.5 text-[13px]">
                    @foreach ($navItems as $item)
                        <a href="{{ route($item['route']) }}" class="transition-colors hover:text-gold">{{ $item['label'] }}</a>
                    @endforeach
                </div>
            </div>

            <div>
                <p class="text-2xs font-semibold uppercase tracking-[0.16em] text-white/40">Informasi</p>
                <div class="mt-4 space-y-2.5 text-[13px]">
                    <p class="flex items-start gap-2.5">
                        <x-ui.icon name="user" class="mt-0.5 h-3.5 w-3.5 shrink-0 text-white/35" />
                        <span>Kepala Sekolah<br><span class="text-white">{{ \App\Models\Setting::get(\App\Support\SchoolSetting::HEADMASTER, '-') }}</span></span>
                    </p>
                    <p class="flex items-start gap-2.5">
                        <x-ui.icon name="clock" class="mt-0.5 h-3.5 w-3.5 shrink-0 text-white/35" />
                        <span>Jam layanan {{ \App\Models\Setting::get(\App\Support\SchoolSetting::CHECK_IN_TIME, '06:30') }}–{{ \App\Models\Setting::get(\App\Support\SchoolSetting::CHECK_OUT_TIME, '14:00') }}</span>
                    </p>
                </div>
            </div>

            <div>
                <p class="text-2xs font-semibold uppercase tracking-[0.16em] text-white/40">Kontak</p>
                <div class="mt-4 space-y-2.5 text-[13px]">
                    <p class="flex items-start gap-2.5">
                        <x-ui.icon name="phone" class="mt-0.5 h-3.5 w-3.5 shrink-0 text-white/35" />
                        <span>{{ \App\Models\Setting::get(\App\Support\SchoolSetting::PHONE, '-') }}</span>
                    </p>
                    <p class="flex items-start gap-2.5">
                        <x-ui.icon name="mail" class="mt-0.5 h-3.5 w-3.5 shrink-0 text-white/35" />
                        <span class="break-all">{{ \App\Models\Setting::get(\App\Support\SchoolSetting::EMAIL, '-') }}</span>
                    </p>
                </div>

                <a href="{{ route('login') }}" class="link-arrow mt-5 text-gold hover:text-gold-light">
                    Portal Absensi
                    <x-ui.icon name="arrow-right" class="h-3.5 w-3.5" />
                </a>
            </div>
        </div>

        <div class="border-t border-white/10">
            <div class="mx-auto flex max-w-6xl flex-col gap-2 px-4 py-5 text-xs text-white/40 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                <p>&copy; {{ date('Y') }} {{ \App\Support\SchoolSetting::name() }}. Seluruh hak cipta dilindungi.</p>
                <p>Sistem Informasi Absensi Sekolah</p>
            </div>
        </div>
    </footer>
</body>
</html>
