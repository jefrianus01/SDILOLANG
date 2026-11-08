<x-app-layout title="Pengguna">
    <div class="space-y-5">
        <p class="text-sm text-gray-500">
            Halaman ini hanya untuk Super Admin. Akun guru dibuat otomatis dari menu Data Guru.
        </p>

        <div class="grid gap-5 xl:grid-cols-3">
            <x-ui.card title="Buat Akun Baru" description="Akun akan wajib mengganti password saat login pertama." class="xl:col-span-1">
                <form method="POST" action="{{ route('users.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <x-input-label for="name" value="Nama" />
                        <x-text-input id="name" name="name" :value="old('name')" class="mt-1 block w-full" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="username" value="Username" />
                        <x-text-input id="username" name="username" :value="old('username')" class="mt-1 block w-full" required autocomplete="off" />
                        <x-input-error :messages="$errors->get('username')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="email" value="Email (opsional)" />
                        <x-text-input id="email" name="email" type="email" :value="old('email')" class="mt-1 block w-full" autocomplete="off" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="role" value="Peran" />
                        <select id="role" name="role" required
                                class="field mt-1">
                            @foreach ($assignableRoles as $value => $label)
                                <option value="{{ $value }}" @selected(old('role') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('role')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="password" value="Password" />
                        <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="password_confirmation" value="Ulangi password" />
                        <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" required autocomplete="new-password" />
                    </div>
                    <button class="btn btn-primary w-full">Buat Akun</button>
                </form>
            </x-ui.card>

            <div class="space-y-5 xl:col-span-2">
                <x-ui.card padding="p-4">
                    <form method="GET" action="{{ route('users.index') }}" class="grid gap-3 sm:grid-cols-3">
                        <div class="sm:col-span-2">
                            <x-input-label for="search" value="Cari nama / username" />
                            <x-text-input id="search" name="search" :value="$filters['search'] ?? ''" class="mt-1 block w-full" />
                        </div>
                        <div>
                            <x-input-label for="filter_role" value="Peran" />
                            <div class="mt-1 flex gap-2">
                                <select id="filter_role" name="role" class="field">
                                    <option value="">Semua</option>
                                    @foreach ($roles as $value => $label)
                                        <option value="{{ $value }}" @selected(($filters['role'] ?? '') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <button class="btn btn-primary btn-sm">Cari</button>
                            </div>
                        </div>
                    </form>
                </x-ui.card>

                <x-ui.card padding="p-0">
                    <div class="overflow-x-auto">
                        <table class="data-table">
                            <thead class="bg-gray-50/80">
                                <tr>
                                    <th class="px-4 py-3">Nama</th>
                                    <th class="px-4 py-3">Username</th>
                                    <th class="px-4 py-3">Peran</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3">Login Terakhir</th>
                                    <th class="px-4 py-3 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @forelse ($users as $user)
                                    <tr>
                                        <td class="px-4 py-3 font-semibold text-gray-900">{{ $user->name }}</td>
                                        <td class="px-4 py-3 font-mono text-gray-700">{{ $user->username }}</td>
                                        <td class="px-4 py-3 text-gray-600">
                                            {{ $user->getRoleNames()->map(fn ($role) => \App\Support\Roles::label($role))->join(', ') ?: '-' }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <x-ui.badge :status="$user->is_active ? 'aktif' : 'nonaktif'" />
                                            @if ($user->must_change_password)
                                                <p class="mt-1 text-xs text-amber-600">Wajib ganti password</p>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-gray-600">{{ $user->last_login_at?->translatedFormat('d M Y H:i') ?? '-' }}</td>
                                        <td class="px-4 py-3">
                                            <div class="flex flex-wrap items-center justify-end gap-2">
                                                @can('resetPassword', $user)
                                                    <form method="POST" action="{{ route('users.reset-password', $user) }}"
                                                          data-confirm="Reset password {{ $user->name }}? Password baru hanya ditampilkan sekali."
                                                          data-confirm-title="Reset password"
                                                          data-confirm-label="Ya, reset"
                                                          data-confirm-tone="warning">
                                                        @csrf
                                                        <button class="btn btn-secondary btn-xs">Reset Password</button>
                                                    </form>
                                                @endcan
                                                @can('toggleActive', $user)
                                                    <form method="POST" action="{{ route('users.toggle-active', $user) }}"
                                                          data-confirm="Ubah status akun {{ $user->name }}?"
                                                          data-confirm-title="Ubah status akun"
                                                          data-confirm-label="Ya, ubah"
                                                          data-confirm-tone="warning">
                                                        @csrf
                                                        <button class="btn btn-xs {{ $user->is_active ? 'btn-danger' : 'btn-secondary' }}">
                                                            {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                                        </button>
                                                    </form>
                                                @endcan
                                                @if ($user->id === auth()->id())
                                                    <span class="text-xs text-gray-400">Akun Anda</span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <x-ui.empty-row :colspan="6" message="Belum ada pengguna." />
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($users->hasPages())
                        <div class="border-t border-gray-100 px-4 py-3">{{ $users->links() }}</div>
                    @endif
                </x-ui.card>
            </div>
        </div>
    </div>
</x-app-layout>
