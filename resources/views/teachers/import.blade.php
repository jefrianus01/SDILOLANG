@php($result = session('import_result'))

<x-app-layout title="Import Guru">
    <div class="mx-auto max-w-4xl space-y-5">
        <a href="{{ route('teachers.index') }}" class="text-sm font-semibold text-brand-600 hover:text-brand-700">&larr; Kembali ke data guru</a>

        <x-ui.card title="Import Data Guru dari Excel" description="Gunakan template resmi agar kolom sesuai.">
            <div class="grid gap-6 md:grid-cols-2">
                <div>
                    <form method="POST" action="{{ route('teachers.import') }}" enctype="multipart/form-data" class="space-y-4">
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
                    <p class="mt-2 font-mono text-xs">nama, nip_nik, jenis_guru, status</p>
                    <ul class="mt-3 list-inside list-disc space-y-1 text-xs">
                        <li>NIP/NIK 16–18 digit angka dan unik.</li>
                        <li>Jenis guru harus ada di master Jenis Guru.</li>
                        <li>Status opsional: aktif / nonaktif.</li>
                        <li>Baris tidak valid dilewati dan dilaporkan nomor barisnya.</li>
                    </ul>
                    <a href="{{ route('teachers.template') }}" class="btn btn-secondary btn-xs mt-4">
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
                        <p class="mt-1 text-2xl font-bold text-emerald-800">{{ $result['imported'] }} guru</p>
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
                                            {{ $failure['data']['nama'] ?: '(nama kosong)' }} &middot; {{ $failure['data']['nip_nik'] ?: '(NIP kosong)' }}
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

                @if (! empty($result['accounts']))
                    <div class="mt-6">
                        <x-ui.alert type="warning" class="mb-3">
                            Password awal di bawah ini <strong>hanya ditampilkan sekali</strong>. Catat dan serahkan kepada guru masing-masing.
                        </x-ui.alert>
                        <div class="overflow-x-auto">
                            <table class="data-table">
                                <thead class="bg-gray-50/80">
                                    <tr>
                                        <th class="px-3 py-2">Nama</th>
                                        <th class="px-3 py-2">Username</th>
                                        <th class="px-3 py-2">Password Awal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach ($result['accounts'] as $account)
                                        <tr>
                                            <td class="px-3 py-2 text-gray-700">{{ $account['name'] }}</td>
                                            <td class="px-3 py-2 font-mono text-gray-700">{{ $account['username'] }}</td>
                                            <td class="px-3 py-2 font-mono font-bold text-amber-700">{{ $account['password'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </x-ui.card>
        @endif
    </div>
</x-app-layout>
