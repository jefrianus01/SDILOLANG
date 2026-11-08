<x-public-layout title="Profil Sekolah">
    @php($schoolName = \App\Support\SchoolSetting::name())

    <section class="border-b border-gray-200 bg-gradient-to-b from-brand-50/80 to-white">
        <div class="mx-auto max-w-6xl px-4 py-12 sm:px-6 lg:py-16">
            <p class="flex items-center gap-3 text-2xs font-semibold uppercase tracking-[0.22em] text-gold-dark">
                <span class="h-px w-8 bg-gold" aria-hidden="true"></span>
                Profil
            </p>
            <h1 class="mt-4 max-w-3xl text-balance font-display text-[40px] font-semibold leading-[1.08] text-ink sm:text-[52px]">
                {{ $schoolName }}
            </h1>
            <p class="mt-4 max-w-2xl text-[15px] leading-8 text-gray-600">
                {{ \App\Support\SchoolSetting::address() ?: 'Alamat sekolah belum diatur.' }}
            </p>
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-4 py-12 sm:px-6">
        <div class="grid gap-px overflow-hidden rounded-xl border border-gray-200 bg-gray-200 sm:grid-cols-3">
            <div class="bg-white px-6 py-6">
                <p class="text-2xs font-semibold uppercase tracking-[0.14em] text-gray-400">NPSN</p>
                <p class="mt-2 font-display text-[26px] font-semibold leading-none text-ink tabular-nums">
                    {{ \App\Models\Setting::get(\App\Support\SchoolSetting::NPSN, '-') }}
                </p>
            </div>
            <div class="bg-white px-6 py-6">
                <p class="text-2xs font-semibold uppercase tracking-[0.14em] text-gray-400">Kepala Sekolah</p>
                <p class="mt-2 text-[15px] font-semibold leading-6 text-ink">
                    {{ \App\Models\Setting::get(\App\Support\SchoolSetting::HEADMASTER, '-') }}
                </p>
            </div>
            <div class="bg-white px-6 py-6">
                <p class="text-2xs font-semibold uppercase tracking-[0.14em] text-gray-400">Kontak</p>
                <p class="mt-2 text-[15px] font-semibold text-ink">
                    {{ \App\Models\Setting::get(\App\Support\SchoolSetting::PHONE, '-') }}
                </p>
                <p class="mt-0.5 break-all text-[13px] text-gray-500">{{ \App\Models\Setting::get(\App\Support\SchoolSetting::EMAIL, '-') }}</p>
            </div>
        </div>

        <div class="mt-10 grid gap-6 lg:grid-cols-2">
            <div class="rounded-xl border border-gray-200 bg-white p-7 shadow-card">
                <p class="flex items-center gap-3 text-2xs font-semibold uppercase tracking-[0.18em] text-gold-dark">
                    <span class="font-display text-sm">01</span>
                    <span class="h-px w-8 bg-gold/60"></span>
                    Visi
                </p>
                <p class="mt-4 text-[15px] leading-8 text-gray-600">
                    {{ \App\Models\Setting::get(\App\Support\SchoolSetting::VISION, '-') }}
                </p>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-7 shadow-card">
                <p class="flex items-center gap-3 text-2xs font-semibold uppercase tracking-[0.18em] text-gold-dark">
                    <span class="font-display text-sm">02</span>
                    <span class="h-px w-8 bg-gold/60"></span>
                    Misi
                </p>
                <div class="mt-4 whitespace-pre-line text-[15px] leading-8 text-gray-600">
                    {{ \App\Models\Setting::get(\App\Support\SchoolSetting::MISSION, '-') }}
                </div>
            </div>
        </div>

        <div class="relative mt-12 overflow-hidden rounded-2xl bg-ink px-8 py-10 text-white sm:px-10">
            <div class="hero-lines absolute inset-0 opacity-50" aria-hidden="true"></div>
            <div class="relative">
                <p class="text-2xs font-semibold uppercase tracking-[0.2em] text-gold">Alamat sekolah</p>
                <p class="mt-3 max-w-2xl text-[15px] leading-7 text-white/75">
                    {{ \App\Support\SchoolSetting::address() ?: 'Alamat sekolah belum diatur.' }}
                </p>
                <p class="mt-4 text-[13px] text-white/50">
                    Website: {{ \App\Models\Setting::get(\App\Support\SchoolSetting::WEBSITE, '-') }}
                </p>
            </div>
        </div>
    </section>
</x-public-layout>
