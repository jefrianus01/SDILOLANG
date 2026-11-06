@php($result = session('import_result'))

<x-app-layout title="Import Siswa">
    <div class="mx-auto max-w-4xl space-y-5">
        <a href="{{ route('students.index') }}" class="text-sm font-semibold text-brand-600 hover:text-brand-700">&larr; Kembali ke data siswa</a>

        <x-ui.card title="Import Data Siswa dari Excel" :description="'Semua siswa akan dimasukkan ke kelas '.$classroom->name.'.'">
            <div class="grid gap-6 md:grid-cols-2">
                <div>
                    <form method="POST" action="{{ route('students.import') }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div>
                            <x-input-label for="file" value="File Excel / CSV" />
                            <input id="file" type="file" name="file" accept=".xlsx,.xls,.csv" required
                                   class="mt-1 block w-full field-file">
                            <x-input-error :messages="$errors->get('file')" class="mt-2" />
                        </div>
                        <button class="btn btn-primary">Proses Import</button>
                    </form>
                </div>

                <div class="rounded-lg bg-gray-50 p-4 text-sm text-gray-600 ring-1 ring-gray-200">
                    <p class="font-semibold text-gray-800">Kolom template</p>
                    <p class="mt-2 font-mono text-xs">nomor_induk_siswa, nama</p>
                    <ul class="mt-3 list-inside list-disc space-y-1 text-xs">
                        <li>Nomor induk siswa wajib diisi dan unik.</li>
                        <li>Nama siswa wajib diisi.</li>
                        <li>Kelas diisi otomatis sesuai kelas Anda.</li>
                        <li>Baris tidak valid dilewati dan dilaporkan nomor barisnya.</li>
                    </ul>
                    <a href="{{ route('students.template') }}" class="btn btn-secondary btn-xs mt-4">
                        Unduh Template Excel
                    </a>
                </div>
            </div>
        </x-ui.card>

        @if ($result)
            <x-ui.card title="Hasil Import">
                <div class="grid gap-3 sm:grid-cols-2">
                    <div class="rounded-lg bg-emerald-50 p-4 ring-1 ring-emerald-200">
                        <p class="text-sm text-emerald-700">Berhasil diimport</p>
                        <p class="mt-1 text-2xl font-bold text-emerald-800">{{ $result['imported'] }} siswa</p>
                    </div>
                    <div class="rounded-lg bg-rose-50 p-4 ring-1 ring-rose-200">
                        <p class="text-sm text-rose-700">Baris ditolak</p>
                        <p class="mt-1 text-2xl font-bold text-rose-800">{{ count($result['failures']) }} baris</p>
                    </div>
                </div>

                @if (! empty($result['failures']))
                    <div class="mt-5 overflow-x-auto">
                        <table class="data-table">
                            <thead class="bg-gray-50/80">
                                <tr>
                                    <th class="px-3 py-2">Baris</th>
                                    <th class="px-3 py-2">Data</th>
                                    <th class="px-3 py-2">Kesalahan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($result['failures'] as $failure)
                                    <tr>
                                        <td class="px-3 py-2 font-semibold text-rose-700">#{{ $failure['row'] }}</td>
                                        <td class="px-3 py-2 text-xs text-gray-600">
                                            {{ $failure['data']['nama'] ?: '(nama kosong)' }} &middot; {{ $failure['data']['nomor_induk_siswa'] ?: '(NIS kosong)' }}
                                        </td>
                                        <td class="px-3 py-2 text-xs text-gray-700">
                                            <ul class="list-inside list-disc space-y-0.5">
                                                @foreach ($failure['messages'] as $message)
                                                    <li>{{ $message }}</li>
                                                @endforeach
                                            </ul>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </x-ui.card>
        @endif
    </div>
</x-app-layout>
