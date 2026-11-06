<x-app-layout title="Edit Guru">
    <div class="mx-auto max-w-2xl space-y-5">
        <a href="{{ route('teachers.index') }}" class="text-sm font-semibold text-brand-600 hover:text-brand-700">&larr; Kembali ke data guru</a>

        <x-ui.card title="Edit Data Guru" :description="'Username login: '.$teacher->user?->username">
            <form method="POST" action="{{ route('teachers.update', $teacher) }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <x-input-label for="nama" value="Nama Lengkap Guru" />
                    <x-text-input id="nama" name="nama" :value="old('nama', $teacher->nama)" class="mt-1 block w-full" required autofocus />
                    <x-input-error :messages="$errors->get('nama')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="nip_nik" value="NIP / NIK" />
                    <x-text-input id="nip_nik" name="nip_nik" :value="old('nip_nik', $teacher->nip_nik)" class="mt-1 block w-full font-mono" required inputmode="numeric" maxlength="18" />
                    <p class="mt-1 text-xs text-gray-500">Mengubah NIP/NIK juga mengubah username login guru.</p>
                    <x-input-error :messages="$errors->get('nip_nik')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="teacher_type_id" value="Guru Apa" />
                    <select id="teacher_type_id" name="teacher_type_id" class="field mt-1" required>
                        @foreach ($types as $type)
                            <option value="{{ $type->id }}" @selected((string) old('teacher_type_id', $teacher->teacher_type_id) === (string) $type->id)>{{ $type->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('teacher_type_id')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="status" value="Status" />
                    <select id="status" name="status" class="field mt-1">
                        @foreach ($statuses as $status)
                            <option value="{{ $status->value }}" @selected(old('status', $teacher->status->value) === $status->value)>{{ $status->label() }}</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500">Status nonaktif akan menonaktifkan akun login guru.</p>
                    <x-input-error :messages="$errors->get('status')" class="mt-2" />
                </div>

                <div class="flex items-center justify-end gap-2">
                    <a href="{{ route('teachers.index') }}" class="btn btn-secondary">Batal</a>
                    <button class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </x-ui.card>
    </div>
</x-app-layout>
