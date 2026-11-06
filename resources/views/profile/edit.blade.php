@php
    $user = auth()->user();
    $roleLabels = $user->getRoleNames()->map(fn ($role) => \App\Support\Roles::label($role))->join(', ');
@endphp

<x-app-layout title="Profil & Password">
    <div class="mx-auto max-w-4xl space-y-5">
        <p class="text-sm text-gray-500">Anda hanya dapat mengubah foto profil dan password. Data nama, NIP/NIK, dan peran diatur oleh Kepala Sekolah.</p>

        <div class="grid gap-5 lg:grid-cols-2">
            <x-ui.card title="Foto Profil">
                <div class="flex items-center gap-4">
                    @if ($user->photo_path)
                        <img src="{{ Storage::disk('public')->url($user->photo_path) }}" alt="Foto {{ $user->name }}" class="h-20 w-20 rounded-full object-cover ring-2 ring-gray-100">
                    @else
                        <span class="flex h-20 w-20 items-center justify-center rounded-full bg-brand-100 text-xl font-bold text-brand-700">
                            {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($user->name, 0, 2)) }}
                        </span>
                    @endif
                    <div class="min-w-0">
                        <p class="font-semibold text-gray-900">{{ $user->name }}</p>
                        <p class="font-mono text-sm text-gray-500">{{ $user->username }}</p>
                        <p class="text-xs text-gray-500">{{ $roleLabels }}</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-5 space-y-3">
                    @csrf
                    @method('PATCH')
                    <div>
                        <x-input-label for="photo" value="Ganti foto" />
                        <input id="photo" type="file" name="photo" accept=".jpg,.jpeg,.png,.webp"
                               class="mt-1 block w-full field-file">
                        <p class="mt-1 text-xs text-gray-500">JPG, PNG, atau WEBP maksimal 2 MB.</p>
                        <x-input-error :messages="$errors->get('photo')" class="mt-2" />
                    </div>
                    <button class="btn btn-primary">Unggah Foto</button>
                </form>

                @if ($user->photo_path)
                    <form method="POST" action="{{ route('profile.photo.destroy') }}" class="mt-3"
                          onsubmit="return confirm('Hapus foto profil?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger">Hapus Foto</button>
                    </form>
                @endif
            </x-ui.card>

            <x-ui.card title="Ganti Password" description="Gunakan password yang kuat dan mudah Anda ingat.">
                <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="current_password" value="Password saat ini" />
                        <x-text-input id="current_password" name="current_password" type="password" class="mt-1 block w-full" autocomplete="current-password" required />
                        <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="password" value="Password baru" />
                        <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" required />
                        <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="password_confirmation" value="Ulangi password baru" />
                        <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" required />
                        <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
                    </div>

                    <button class="btn btn-primary">Simpan Password</button>
                </form>
            </x-ui.card>
        </div>

        <x-ui.card title="Informasi Akun" padding="p-0">
            <dl class="divide-y divide-gray-100 text-sm">
                <div class="flex items-center justify-between gap-4 px-4 py-3">
                    <dt class="text-gray-500">Nama</dt>
                    <dd class="font-semibold text-gray-900">{{ $user->name }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4 px-4 py-3">
                    <dt class="text-gray-500">Username (NIP/NIK)</dt>
                    <dd class="font-mono font-semibold text-gray-900">{{ $user->username }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4 px-4 py-3">
                    <dt class="text-gray-500">Peran</dt>
                    <dd class="font-semibold text-gray-900">{{ $roleLabels }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4 px-4 py-3">
                    <dt class="text-gray-500">Login terakhir</dt>
                    <dd class="font-semibold text-gray-900">{{ $user->last_login_at?->translatedFormat('d M Y H:i') ?? '-' }}</dd>
                </div>
            </dl>
        </x-ui.card>
    </div>
</x-app-layout>
