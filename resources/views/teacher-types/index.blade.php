<x-app-layout title="Jenis Guru">
    <div class="space-y-5">
        <x-ui.card title="Tambah Jenis Guru" description="Master data jenis guru yang dapat dipilih saat menambah data guru.">
            <form method="POST" action="{{ route('teacher-types.store') }}" class="grid gap-3 md:grid-cols-[1.2fr_1.5fr_auto]">
                @csrf
                <div>
                    <x-input-label for="name" value="Nama jenis guru" />
                    <x-text-input id="name" name="name" :value="old('name')" class="mt-1 block w-full" required placeholder="Contoh: Guru Seni Budaya" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="description" value="Deskripsi (opsional)" />
                    <x-text-input id="description" name="description" :value="old('description')" class="mt-1 block w-full" />
                </div>
                <div class="flex items-end gap-2">
                    <label class="flex items-center gap-2 pb-2 text-sm text-gray-600">
                        <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                        Aktif
                    </label>
                    <button class="btn btn-primary">Tambah</button>
                </div>
            </form>
        </x-ui.card>

        <x-ui.card title="Daftar Jenis Guru" padding="p-0">
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead class="bg-gray-50/80">
                        <tr>
                            <th class="px-4 py-3 w-12">No</th>
                            <th class="px-4 py-3">Jenis Guru</th>
                            <th class="px-4 py-3">Deskripsi</th>
                            <th class="px-4 py-3 text-center">Dipakai</th>
                            <th class="px-4 py-3 text-center">Aktif</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($types as $type)
                            @php($formId = 'type-'.$type->id)
                            <tr>
                                <td class="px-4 py-3 text-gray-500">{{ $types->firstItem() + $loop->index }}</td>
                                <td class="px-4 py-3">
                                    <input form="{{ $formId }}" type="text" name="name" value="{{ $type->name }}"
                                           class="w-full field">
                                </td>
                                <td class="px-4 py-3">
                                    <input form="{{ $formId }}" type="text" name="description" value="{{ $type->description }}"
                                           class="w-full field">
                                </td>
                                <td class="px-4 py-3 text-center text-gray-700">{{ $type->teachers_count }}</td>
                                <td class="px-4 py-3 text-center">
                                    <input form="{{ $formId }}" type="checkbox" name="is_active" value="1" @checked($type->is_active)
                                           class="rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        <form id="{{ $formId }}" method="POST" action="{{ route('teacher-types.update', $type) }}">
                                            @csrf
                                            @method('PUT')
                                            <button class="btn btn-secondary btn-xs">Simpan</button>
                                        </form>
                                        <form method="POST" action="{{ route('teacher-types.destroy', $type) }}"
                                              onsubmit="return confirm('Hapus jenis guru {{ $type->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger btn-xs">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <x-ui.empty-row :colspan="6" message="Belum ada jenis guru." />
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($types->hasPages())
                <div class="border-t border-gray-100 px-4 py-3">{{ $types->links() }}</div>
            @endif
        </x-ui.card>
    </div>
</x-app-layout>
