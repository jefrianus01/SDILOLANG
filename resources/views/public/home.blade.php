<x-public-layout>
    @php
        $schoolName = \App\Support\SchoolSetting::name();
        $vision = \App\Models\Setting::get(\App\Support\SchoolSetting::VISION, 'Terwujudnya peserta didik yang beriman, berkarakter, dan berprestasi.');
        $mission = \App\Models\Setting::get(\App\Support\SchoolSetting::MISSION, 'Menyelenggarakan pembelajaran aktif dan menyenangkan.');
        $description = \App\Models\Setting::get(\App\Support\SchoolSetting::DESCRIPTION, 'Sekolah dasar yang membentuk generasi berkarakter dan berprestasi.');

        $heroSlides = collect([
            'school/gmabra lb1.jpeg',
            'school/gambar lb2.jpeg',
            'school/gambar lb3.jpeg',
        ])
            ->filter(fn ($path) => is_file(public_path('storage/'.$path)))
            ->take(3)
            ->map(fn ($path) => Storage::disk('public')->url(implode('/', array_map('rawurlencode', explode('/', $path)))))
            ->values();
    @endphp

    {{-- Hero --}}
    <section class="relative isolate min-h-[560px] overflow-hidden bg-brand-800 lg:min-h-[640px]">
        @if ($heroSlides->isNotEmpty())
            @php
                $slideCount = $heroSlides->count();
                $slideDuration = $slideCount * 8;
            @endphp

            <div class="hero-slideshow" aria-hidden="true">
                @foreach ($heroSlides as $index => $slide)
                    @php
                        $slideDelay = -((($slideCount - $index) % $slideCount) * 8);
                        $slideModifier = $slideCount === 1 ? 'hero-slide--still' : ($slideCount === 3 ? 'hero-slide--three' : '');
                    @endphp

                    <div class="hero-slide {{ $slideModifier }}"
                         style="background-image: url('{{ $slide }}'); --hero-duration: {{ $slideDuration }}s; --hero-delay: {{ $slideDelay }}s;"></div>
                @endforeach
            </div>
        @else
            <div class="absolute inset-0 bg-gradient-to-br from-brand-700 via-brand-600 to-brand-500" aria-hidden="true"></div>
        @endif

        <div class="absolute inset-0 bg-gradient-to-b from-ink/85 via-ink/70 to-ink/45 lg:bg-gradient-to-r lg:from-ink/90 lg:via-ink/65 lg:to-ink/25" aria-hidden="true"></div>
        <div class="paper-radial absolute inset-0 opacity-35" aria-hidden="true"></div>

        <div class="relative mx-auto max-w-6xl px-4 pb-16 pt-16 sm:px-6 lg:pb-24 lg:pt-24">
            <div class="grid items-start gap-12 lg:grid-cols-12 lg:gap-16">
                <div class="lg:col-span-7">
                    <p class="flex items-center gap-3 text-2xs font-semibold uppercase tracking-[0.22em] text-gold">
                        <span class="h-px w-10 bg-gold/70" aria-hidden="true"></span>
                        Selamat datang
                    </p>

                    <h1 class="mt-5 text-balance font-display text-[44px] font-semibold leading-[1.04] tracking-tight text-white drop-shadow-sm sm:text-[56px] lg:text-[64px]">
                        {{ $schoolName }}
                    </h1>

                    <p class="mt-6 max-w-xl text-[15px] leading-8 text-white/80 sm:text-base">
                        {{ $description }}
                    </p>

                    <div class="mt-9 flex flex-wrap items-center gap-3">
                        <a href="{{ route('school.profile') }}" class="btn-gold">
                            Profil Sekolah
                        </a>
                        <a href="{{ route('news.index') }}" class="btn-outline-light">
                            Berita Terbaru
                            <x-ui.icon name="arrow-right" class="h-3.5 w-3.5" />
                        </a>
                    </div>

                    <div class="mt-10 flex flex-wrap items-center gap-x-6 gap-y-2 text-xs text-white/85">
                        <span class="inline-flex items-center gap-2">
                            <x-ui.icon name="map-pin" class="h-3.5 w-3.5 text-gold/90" />
                            {{ \App\Support\SchoolSetting::address() ?: 'Alamat sekolah' }}
                        </span>
                        <span class="hidden h-3 w-px bg-white/25 sm:block"></span>
                        <span>NPSN {{ \App\Models\Setting::get(\App\Support\SchoolSetting::NPSN, '-') }}</span>
                    </div>
                </div>

                <div class="lg:col-span-5">
                    <div class="overflow-hidden rounded-xl border border-white/25 bg-ink/30 shadow-soft backdrop-blur-[2px]">
                        <div class="border-b border-white/20 px-6 py-5">
                            <p class="text-2xs font-semibold uppercase tracking-[0.16em] text-gold">Sekilas sekolah</p>
                            <p class="mt-1 font-display text-[26px] font-semibold leading-tight text-white drop-shadow-sm">Data pokok</p>
                        </div>

                        <dl class="divide-y divide-white/15 text-sm">
                            <div class="flex items-start justify-between gap-6 px-6 py-3.5">
                                <dt class="text-white/65">Kepala Sekolah</dt>
                                <dd class="text-right font-semibold text-white">{{ \App\Models\Setting::get(\App\Support\SchoolSetting::HEADMASTER, '-') }}</dd>
                            </div>
                            <div class="flex items-start justify-between gap-6 px-6 py-3.5">
                                <dt class="shrink-0 text-white/65">Telepon</dt>
                                <dd class="text-right font-medium text-white/90">{{ \App\Models\Setting::get(\App\Support\SchoolSetting::PHONE, '-') }}</dd>
                            </div>
                            <div class="flex items-start justify-between gap-6 px-6 py-3.5">
                                <dt class="shrink-0 text-white/65">Email</dt>
                                <dd class="break-all text-right font-medium text-white/90">{{ \App\Models\Setting::get(\App\Support\SchoolSetting::EMAIL, '-') }}</dd>
                            </div>
                            <div class="flex items-start justify-between gap-6 px-6 py-3.5">
                                <dt class="shrink-0 text-white/65">Alamat</dt>
                                <dd class="text-right font-medium leading-6 text-white/90">{{ \App\Support\SchoolSetting::address() ?: '-' }}</dd>
                            </div>
                        </dl>

                        <div class="border-t border-white/20 px-6 py-4">
                            <a href="{{ route('school.profile') }}" class="link-arrow text-gold transition-colors hover:text-white">
                                Profil lengkap
                                <x-ui.icon name="arrow-right" class="h-3.5 w-3.5" />
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Angka ringkas --}}
    <section class="border-y border-gray-200 bg-brand-50/60">
        <div class="mx-auto grid max-w-6xl grid-cols-2 gap-px bg-gray-200 px-0 lg:grid-cols-4">
            <div class="bg-white px-6 py-7">
                <p class="text-2xs font-semibold uppercase tracking-[0.14em] text-gray-400">Guru aktif</p>
                <p class="mt-2 font-display text-[38px] font-semibold leading-none text-ink tabular-nums">{{ $teacherCount }}</p>
            </div>
            <div class="bg-white px-6 py-7">
                <p class="text-2xs font-semibold uppercase tracking-[0.14em] text-gray-400">Siswa aktif</p>
                <p class="mt-2 font-display text-[38px] font-semibold leading-none text-ink tabular-nums">{{ $studentCount }}</p>
            </div>
            <div class="bg-white px-6 py-7">
                <p class="text-2xs font-semibold uppercase tracking-[0.14em] text-gray-400">Rombongan belajar</p>
                <p class="mt-2 font-display text-[38px] font-semibold leading-none text-ink tabular-nums">{{ $classrooms->count() }}</p>
            </div>
            <div class="flex flex-col justify-center bg-white px-6 py-7">
                <a href="{{ route('gallery.index') }}" class="link-arrow text-brand-700 transition-colors hover:text-brand-900">
                    Lihat galeri
                    <x-ui.icon name="arrow-right" class="h-3.5 w-3.5" />
                </a>
                <p class="mt-2 text-xs leading-5 text-gray-500">Dokumentasi kegiatan belajar dan acara sekolah.</p>
            </div>
        </div>
    </section>

    {{-- Visi & misi --}}
    <section class="mx-auto max-w-6xl px-4 py-16 sm:px-6 lg:py-20">
        <div class="grid gap-10 lg:grid-cols-2 lg:gap-16">
            <div>
                <p class="flex items-center gap-3 text-2xs font-semibold uppercase tracking-[0.18em] text-gold-dark">
                    <span class="font-display text-sm text-gold-dark">01</span>
                    <span class="h-px w-8 bg-gold/60"></span>
                    Visi
                </p>
                <h2 class="mt-4 font-display text-[34px] font-semibold leading-tight text-ink">Arah pendidikan kami</h2>
                <p class="mt-5 text-[15px] leading-8 text-gray-600">{{ $vision }}</p>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-7 shadow-card sm:p-8">
                <p class="flex items-center gap-3 text-2xs font-semibold uppercase tracking-[0.18em] text-gold-dark">
                    <span class="font-display text-sm">02</span>
                    <span class="h-px w-8 bg-gold/60"></span>
                    Misi
                </p>
                <div class="mt-4 whitespace-pre-line text-[15px] leading-8 text-gray-600">{{ $mission }}</div>
            </div>
        </div>
    </section>

    {{-- Kelas --}}
    <section class="border-y border-gray-200 bg-parchment/60 py-16">
        <div class="mx-auto max-w-6xl px-4 sm:px-6">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="text-2xs font-semibold uppercase tracking-[0.18em] text-gold-dark">Rombongan belajar</p>
                    <h2 class="mt-2 font-display text-[34px] font-semibold leading-tight text-ink">Kelas 1 sampai 6</h2>
                </div>
                <a href="{{ route('school.profile') }}" class="link-arrow text-brand-700 transition-colors hover:text-brand-900">
                    Profil lengkap
                    <x-ui.icon name="arrow-right" class="h-3.5 w-3.5" />
                </a>
            </div>

            <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @forelse ($classrooms as $classroom)
                    <div class="group rounded-xl border border-gray-200 bg-white shadow-card p-5 transition-colors hover:border-brand-300">
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-[15px] font-semibold text-ink">{{ $classroom->name }}</p>
                            <span class="chip">{{ $classroom->students_count }} siswa</span>
                        </div>

                        <p class="mt-4 text-2xs font-semibold uppercase tracking-[0.12em] text-gray-400">Wali kelas</p>
                        <p class="mt-1 text-sm font-medium text-gray-800">{{ $classroom->waliKelas?->nama ?? 'Belum ditunjuk' }}</p>
                    </div>
                @empty
                    <div class="rounded-xl border border-dashed border-gray-300 bg-white p-8 text-sm text-gray-500 sm:col-span-2 lg:col-span-3">
                        Data kelas belum tersedia untuk tahun ajaran aktif.
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- Berita --}}
    <section class="mx-auto max-w-6xl px-4 py-16 sm:px-6 lg:py-20">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-2xs font-semibold uppercase tracking-[0.18em] text-gold-dark">Kabar sekolah</p>
                <h2 class="mt-2 font-display text-[34px] font-semibold leading-tight text-ink">Berita terbaru</h2>
            </div>
            <a href="{{ route('news.index') }}" class="link-arrow text-brand-700 transition-colors hover:text-brand-900">
                Semua berita
                <x-ui.icon name="arrow-right" class="h-3.5 w-3.5" />
            </a>
        </div>

        <div class="mt-8 grid gap-6 md:grid-cols-3">
            @forelse ($news as $item)
                <article class="group overflow-hidden rounded-xl border border-gray-200 bg-white shadow-card transition-colors hover:border-gray-300">
                    <a href="{{ route('news.show', $item->slug) }}" class="block" tabindex="-1" aria-hidden="true">
                        @if ($item->image_path)
                            <img src="{{ Storage::disk('public')->url($item->image_path) }}" alt="{{ $item->title }}"
                                 class="h-44 w-full object-cover">
                        @else
                            <div class="flex h-44 items-center justify-center border-b border-gray-100 bg-parchment">
                                <span class="text-2xs font-semibold uppercase tracking-[0.2em] text-gold-dark">Berita Sekolah</span>
                            </div>
                        @endif
                    </a>

                    <div class="p-5">
                        <p class="text-2xs font-semibold uppercase tracking-[0.12em] text-gray-400">
                            {{ $item->published_at?->translatedFormat('d F Y') ?? $item->created_at?->translatedFormat('d F Y') }}
                        </p>
                        <h3 class="mt-2 font-display text-[22px] font-semibold leading-snug text-ink">
                            <a href="{{ route('news.show', $item->slug) }}" class="transition-colors hover:text-wine">{{ $item->title }}</a>
                        </h3>
                        <p class="mt-2 line-clamp-3 text-[13px] leading-6 text-gray-600">{{ $item->excerpt }}</p>
                    </div>
                </article>
            @empty
                <div class="rounded-xl border border-dashed border-gray-300 bg-white p-8 text-sm text-gray-500 md:col-span-3">
                    Belum ada berita yang dipublikasikan.
                </div>
            @endforelse
        </div>
    </section>

    {{-- Galeri --}}
    <section class="border-y border-gray-200 bg-brand-50/50 py-16">
        <div class="mx-auto max-w-6xl px-4 sm:px-6">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="text-2xs font-semibold uppercase tracking-[0.18em] text-gold-dark">Dokumentasi</p>
                    <h2 class="mt-2 font-display text-[34px] font-semibold leading-tight text-ink">Momen di sekolah</h2>
                </div>
                <a href="{{ route('gallery.index') }}" class="link-arrow text-brand-700 transition-colors hover:text-brand-900">
                    Semua foto
                    <x-ui.icon name="arrow-right" class="h-3.5 w-3.5" />
                </a>
            </div>

            <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @forelse ($galleries as $gallery)
                    <figure class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-card">
                        <img src="{{ Storage::disk('public')->url($gallery->image_path) }}" alt="{{ $gallery->title }}"
                             class="h-52 w-full object-cover">
                        <figcaption class="p-4">
                            <p class="text-[13px] font-semibold text-ink">{{ $gallery->title }}</p>
                            @if ($gallery->caption)
                                <p class="mt-1 line-clamp-2 text-xs leading-5 text-gray-500">{{ $gallery->caption }}</p>
                            @endif
                        </figcaption>
                    </figure>
                @empty
                    <div class="rounded-xl border border-dashed border-gray-300 bg-white p-8 text-sm text-gray-500 sm:col-span-2 lg:col-span-3">
                        Belum ada foto galeri.
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- Kontak --}}
    <section class="mx-auto max-w-6xl px-4 pt-16 sm:px-6">
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-brand-900 via-ink to-brand-950 px-7 py-12 text-white sm:px-12">
            <div class="hero-lines absolute inset-0 opacity-60" aria-hidden="true"></div>

            <div class="relative grid gap-10 lg:grid-cols-[1.3fr_1fr] lg:items-center">
                <div>
                    <p class="text-2xs font-semibold uppercase tracking-[0.2em] text-gold">Kunjungi kami</p>
                    <h2 class="mt-4 text-balance font-display text-[34px] font-semibold leading-tight text-white sm:text-[40px]">
                        Hubungi {{ $schoolName }}
                    </h2>
                    <p class="mt-4 max-w-lg text-sm leading-7 text-white/60">
                        {{ \App\Support\SchoolSetting::address() ?: 'Silakan hubungi kantor sekolah untuk informasi pendaftaran dan layanan akademik.' }}
                    </p>
                </div>

                <div class="space-y-3 text-sm">
                    <p class="flex items-center gap-3">
                        <x-ui.icon name="phone" class="h-4 w-4 text-gold" />
                        <span class="text-white/80">{{ \App\Models\Setting::get(\App\Support\SchoolSetting::PHONE, '-') }}</span>
                    </p>
                    <p class="flex items-center gap-3">
                        <x-ui.icon name="mail" class="h-4 w-4 text-gold" />
                        <span class="break-all text-white/80">{{ \App\Models\Setting::get(\App\Support\SchoolSetting::EMAIL, '-') }}</span>
                    </p>
                    <p class="flex items-center gap-3">
                        <x-ui.icon name="clock" class="h-4 w-4 text-gold" />
                        <span class="text-white/80">Senin–Sabtu, {{ \App\Models\Setting::get(\App\Support\SchoolSetting::CHECK_IN_TIME, '06:30') }}–{{ \App\Models\Setting::get(\App\Support\SchoolSetting::CHECK_OUT_TIME, '14:00') }}</span>
                    </p>

                    <a href="{{ route('login') }}" class="btn-gold mt-5 w-full sm:w-auto">
                        Portal Absensi
                    </a>
                </div>
            </div>
        </div>
    </section>
</x-public-layout>
