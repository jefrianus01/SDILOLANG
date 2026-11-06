<x-guest-layout>
    <div class="mb-7">
        <p class="text-2xs font-semibold uppercase tracking-[0.18em] text-gold-dark">Portal Akademik</p>
        <h2 class="mt-1.5 text-[22px] font-semibold leading-tight text-gray-900">Masuk ke akun Anda</h2>
        <p class="mt-1.5 text-[13px] leading-5 text-gray-500">Gunakan username yang diberikan sekolah. Guru memakai NIP/NIK.</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4" x-data="{ showPassword: false }">
        @csrf

        <div>
            <x-input-label for="username" value="Username / NIP / NIK" />
            <x-text-input id="username" class="mt-1 block w-full" type="text" name="username" :value="old('username')"
                          required autofocus autocomplete="username" placeholder="Contoh: 198502032010011002" />
            <x-input-error :messages="$errors->get('username')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" value="Password" />
            <div class="relative mt-1">
                <x-text-input id="password" class="block w-full pr-11" type="password" name="password"
                              x-bind:type="showPassword ? 'text' : 'password'"
                              required autocomplete="current-password" placeholder="Masukkan password" />
                <button type="button" @click="showPassword = ! showPassword"
                        class="absolute inset-y-0 right-0 flex w-11 items-center justify-center text-gray-400 transition-colors hover:text-gray-600"
                        :aria-label="showPassword ? 'Sembunyikan password' : 'Tampilkan password'">
                    <x-ui.icon name="eye" class="h-4 w-4" />
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between pt-1">
            <label for="remember_me" class="inline-flex cursor-pointer items-center gap-2">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-brand-700 shadow-none focus:ring-brand-500/40" name="remember">
                <span class="text-[13px] text-gray-600">Ingat saya</span>
            </label>

            <span class="text-2xs text-gray-400">Lupa password? Hubungi admin</span>
        </div>

        <x-primary-button class="mt-2 w-full py-2.5">
            Masuk
            <x-ui.icon name="arrow-right" class="h-4 w-4" />
        </x-primary-button>
    </form>

    <details class="group mt-6 rounded-lg border border-gray-200 bg-gray-50/70">
        <summary class="flex cursor-pointer list-none items-center justify-between gap-2 px-4 py-3 text-[13px] font-medium text-gray-600 [&::-webkit-details-marker]:hidden">
            Akun demo untuk pengujian
            <x-ui.icon name="chevron-down" class="h-4 w-4 text-gray-400 transition-transform group-open:rotate-180" />
        </summary>

        <div class="space-y-2 border-t border-gray-200 px-4 py-3 text-xs text-gray-600">
            <div class="flex items-baseline justify-between gap-3">
                <span class="text-gray-500">Super Admin</span>
                <span class="font-mono text-gray-800">superadmin / Super#2026</span>
            </div>
            <div class="flex items-baseline justify-between gap-3">
                <span class="text-gray-500">Kepala Sekolah</span>
                <span class="font-mono text-gray-800">197001012000011001 / Guru#2026</span>
            </div>
            <div class="flex items-baseline justify-between gap-3">
                <span class="text-gray-500">Wali Kelas</span>
                <span class="font-mono text-gray-800">198502032010011002 / Guru#2026</span>
            </div>
        </div>
    </details>
</x-guest-layout>
