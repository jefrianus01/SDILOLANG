<x-app-layout title="Tambah Siswa">
    <div class="mx-auto max-w-2xl space-y-5">
        <a href="{{ route('students.index') }}" class="text-sm font-semibold text-brand-600 hover:text-brand-700">&larr; Kembali ke data siswa</a>

        <x-ui.card title="Tambah Siswa" :description="'Siswa akan dimasukkan ke kelas '.$classroom->name.' (wali kelas: '.auth()->user()->name.')'">
            <form method="POST" action="{{ route('students.store') }}" class="space-y-4">
                @csrf

                <div>
                    <x-input-label for="nomor_induk_siswa" value="Nomor Induk Siswa" />
                    <x-text-input id="nomor_induk_siswa" name="nomor_induk_siswa" :value="old('nomor_induk_siswa')" class="mt-1 block w-full" required autofocus autocomplete="off" placeholder="Contoh: 2026001" />
                    <x-input-error :messages="$errors->get('nomor_induk_siswa')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="nama" value="Nama Siswa" />
                    <x-text-input id="nama" name="nama" :value="old('nama')" class="mt-1 block w-full" required autocomplete="off" placeholder="Nama lengkap siswa" />
                    <x-input-error :messages="$errors->get('nama')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="classroom" value="Kelas" />
                    <input id="classroom" type="text" value="{{ $classroom->name }}" disabled
                           class="mt-1 block w-full rounded-lg border-gray-200 bg-gray-50 text-sm text-gray-600">
                    <p class="mt-1 text-xs text-gray-500">Kelas mengikuti wali kelas yang sedang login.</p>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button class="btn btn-primary">Simpan</button>
                    <a href="{{ route('students.index') }}" class="text-sm font-semibold text-gray-500 hover:text-gray-700">Batal</a>
                </div>
            </form>
        </x-ui.card>
    </div>
</x-app-layout>
