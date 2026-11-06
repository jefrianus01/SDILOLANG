<x-app-layout title="Edit Siswa">
    <div class="mx-auto max-w-2xl space-y-5">
        <a href="{{ route('students.index') }}" class="text-sm font-semibold text-brand-600 hover:text-brand-700">&larr; Kembali ke data siswa</a>

        <x-ui.card title="Edit Siswa" :description="$student->classroom?->name">
            <form method="POST" action="{{ route('students.update', $student) }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <x-input-label for="nomor_induk_siswa" value="Nomor Induk Siswa" />
                    <x-text-input id="nomor_induk_siswa" name="nomor_induk_siswa" :value="old('nomor_induk_siswa', $student->nomor_induk_siswa)" class="mt-1 block w-full" required autocomplete="off" />
                    <x-input-error :messages="$errors->get('nomor_induk_siswa')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="nama" value="Nama Siswa" />
                    <x-text-input id="nama" name="nama" :value="old('nama', $student->nama)" class="mt-1 block w-full" required autocomplete="off" />
                    <x-input-error :messages="$errors->get('nama')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="classroom" value="Kelas" />
                    <input id="classroom" type="text" value="{{ $student->classroom?->name ?? '-' }}" disabled
                           class="mt-1 block w-full rounded-lg border-gray-200 bg-gray-50 text-sm text-gray-600">
                    <p class="mt-1 text-xs text-gray-500">Perpindahan kelas hanya dapat dilakukan oleh Super Admin melalui menu Kenaikan Kelas.</p>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button class="btn btn-primary">Perbarui</button>
                    <a href="{{ route('students.index') }}" class="text-sm font-semibold text-gray-500 hover:text-gray-700">Batal</a>
                </div>
            </form>
        </x-ui.card>
    </div>
</x-app-layout>
