<x-app-layout title="Tambah Guru">
    <div class="mx-auto max-w-2xl space-y-5">
        <a href="{{ route('teachers.index') }}" class="text-sm font-semibold text-brand-600 hover:text-brand-700">&larr; Kembali ke data guru</a>

        <x-ui.card title="Form Data Guru" description="Akun login dibuat otomatis dengan username NIP/NIK dan password acak.">
            <form method="POST" action="{{ route('teachers.store') }}" class="space-y-5">
                @csrf

                <div>
                    <x-input-label for="nama" value="Nama Lengkap Guru" />
                    <x-text-input id="nama" name="nama" :value="old('nama')" class="mt-1 block w-full" required autofocus placeholder="Contoh: Guru 1" />
                    <x-input-error :messages="$errors->get('nama')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="nip_nik" value="NIP / NIK" />
                    <x-text-input id="nip_nik" name="nip_nik" :value="old('nip_nik')" class="mt-1 block w-full font-mono" required inputmode="numeric" maxlength="18" placeholder="16-18 digit angka" />
                    <p class="mt-1 text-xs text-gray-500">Hanya angka, 16–18 digit, dan harus unik. Username login memakai nilai ini.</p>
                    <x-input-error :messages="$errors->get('nip_nik')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="teacher_type_id" value="Guru Apa" />
                    <select id="teacher_type_id" name="teacher_type_id" class="field mt-1" required>
                        <option value="">Pilih jenis guru</option>
                        @foreach ($types as $type)
                            <option value="{{ $type->id }}" @selected((string) old('teacher_type_id') === (string) $type->id)>{{ $type->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('teacher_type_id')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="status" value="Status" />
                    <select id="status" name="status" class="field mt-1">
                        @foreach ($statuses as $status)
                            <option value="{{ $status->value }}" @selected(old('status', 'aktif') === $status->value)>{{ $status->label() }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('status')" class="mt-2" />
                </div>

                <x-ui.alert type="info">
                    Password awal dibuat acak dan <strong>hanya ditampilkan sekali</strong> setelah data disimpan.
                    Guru wajib mengganti password saat login pertama.
                </x-ui.alert>

                <div class="flex items-center justify-end gap-2">
                    <a href="{{ route('teachers.index') }}" class="btn btn-secondary">Batal</a>
                    <button class="btn btn-primary">Simpan &amp; Buat Akun</button>
                </div>
            </form>
        </x-ui.card>
    </div>
</x-app-layout>
