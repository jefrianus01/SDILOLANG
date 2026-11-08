<x-app-layout title="Tahun Ajaran">
    <div class="space-y-5">
        <x-ui.card title="Tambah Tahun Ajaran" description="Kelas 1–6 otomatis dibuat untuk setiap tahun ajaran baru.">
            <form method="POST" action="{{ route('academic-years.store') }}" class="grid gap-3 md:grid-cols-[1fr_1fr_1fr_auto]">
                @csrf
                <div>
                    <x-input-label for="name" value="Tahun ajaran" />
                    <x-text-input id="name" name="name" :value="old('name')" class="mt-1 block w-full" required placeholder="2027/2028" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="starts_on" value="Mulai" />
                    <x-text-input id="starts_on" name="starts_on" type="date" :value="old('starts_on')" class="mt-1 block w-full" required />
                    <x-input-error :messages="$errors->get('starts_on')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="ends_on" value="Selesai" />
                    <x-text-input id="ends_on" name="ends_on" type="date" :value="old('ends_on')" class="mt-1 block w-full" required />
                    <x-input-error :messages="$errors->get('ends_on')" class="mt-2" />
                </div>
                <div class="flex items-end">
                    <button class="btn btn-primary">Tambah</button>
                </div>
            </form>
        </x-ui.card>

        <x-ui.card title="Daftar Tahun Ajaran" padding="p-0">
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead class="bg-gray-50/80">
                        <tr>
                            <th class="px-4 py-3">Tahun</th>
                            <th class="px-4 py-3">Periode</th>
                            <th class="px-4 py-3 text-center">Kelas</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($years as $year)
                            @php($formId = 'year-'.$year->id)
                            <tr>
                                <td class="px-4 py-3">
                                    <input form="{{ $formId }}" type="text" name="name" value="{{ $year->name }}"
                                           class="w-28 field font-semibold">
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <input form="{{ $formId }}" type="date" name="starts_on" value="{{ $year->starts_on?->toDateString() }}"
                                               class="field">
                                        <span class="text-gray-400">s.d.</span>
                                        <input form="{{ $formId }}" type="date" name="ends_on" value="{{ $year->ends_on?->toDateString() }}"
                                               class="field">
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center text-gray-700">{{ $year->classrooms_count }}</td>
                                <td class="px-4 py-3 text-center">
                                    @if ($year->is_active)
                                        <x-ui.badge status="aktif" />
                                    @else
                                        <x-ui.badge status="nonaktif" />
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap items-center justify-end gap-2">
                                        <form id="{{ $formId }}" method="POST" action="{{ route('academic-years.update', $year) }}">
                                            @csrf
                                            @method('PUT')
                                            <button class="btn btn-secondary btn-xs">Simpan</button>
                                        </form>
                                        @if (! $year->is_active)
                                            <form method="POST" action="{{ route('academic-years.activate', $year) }}">
                                                @csrf
                                                <button class="btn btn-secondary btn-xs">Aktifkan</button>
                                            </form>
                                        @endif
                                        <form method="POST" action="{{ route('academic-years.destroy', $year) }}"
                                              data-confirm="Hapus tahun ajaran {{ $year->name }}?"
                                              data-confirm-title="Hapus tahun ajaran"
                                              data-confirm-label="Ya, hapus"
                                              data-confirm-tone="danger">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger btn-xs">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <x-ui.empty-row :colspan="5" message="Belum ada tahun ajaran." />
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-ui.card>
    </div>
</x-app-layout>
