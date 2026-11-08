<x-app-layout title="Data Guru">
    <div class="space-y-5">
        <x-ui.page-header
            eyebrow="Data"
            title="Data Guru"
            description="Data guru hanya dapat dikelola oleh Kepala Sekolah (Super Admin sebagai cadangan).">
            <x-slot name="actions">
                @can('create', App\Models\Teacher::class)
                    <a href="{{ route('teachers.create') }}" class="btn btn-primary">+ Tambah Guru</a>
                    <a href="{{ route('teachers.import.form') }}" class="btn btn-secondary">Import Excel</a>
                @endcan
                <a href="{{ route('teachers.export.excel', request()->query()) }}" class="btn btn-secondary">Export Excel</a>
                <a href="{{ route('teachers.export.pdf', request()->query()) }}" class="btn btn-danger">Export PDF</a>
            </x-slot>
        </x-ui.page-header>

        <x-ui.card padding="p-4">
            <form method="GET" action="{{ route('teachers.index') }}" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                <div class="lg:col-span-2">
                    <x-input-label for="search" value="Cari nama / NIP" />
                    <x-text-input id="search" name="search" :value="$filters['search'] ?? ''" class="mt-1 block w-full" placeholder="Ketik nama atau NIP/NIK" />
                </div>
                <div>
                    <x-input-label for="teacher_type_id" value="Guru Apa" />
                    <select id="teacher_type_id" name="teacher_type_id" class="field mt-1">
                        <option value="">Semua jenis</option>
                        @foreach ($types as $type)
                            <option value="{{ $type->id }}" @selected((string) ($filters['teacher_type_id'] ?? '') === (string) $type->id)>{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="status" value="Status" />
                    <div class="mt-1 flex gap-2">
                        <select id="status" name="status" class="field">
                            <option value="">Semua</option>
                            <option value="aktif" @selected(($filters['status'] ?? '') === 'aktif')>Aktif</option>
                            <option value="nonaktif" @selected(($filters['status'] ?? '') === 'nonaktif')>Nonaktif</option>
                        </select>
                        <button class="btn btn-primary">Filter</button>
                    </div>
                </div>
            </form>
        </x-ui.card>

        <x-ui.card padding="p-0">
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead class="bg-gray-50/80">
                        <tr>
                            <th class="px-4 py-3 w-12">No</th>
                            <th class="px-4 py-3">Nama Guru</th>
                            <th class="px-4 py-3">NIP/NIK</th>
                            <th class="px-4 py-3">Guru Apa</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($teachers as $teacher)
                            <tr>
                                <td class="px-4 py-3 text-gray-500">{{ $teachers->firstItem() + $loop->index }}</td>
                                <td class="px-4 py-3">
                                    <p class="font-semibold text-gray-900">{{ $teacher->nama }}</p>
                                    @if ($teacher->classrooms->isNotEmpty())
                                        <p class="text-xs text-gray-500">Wali kelas: {{ $teacher->classrooms->pluck('name')->join(', ') }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-3 font-mono text-gray-700">{{ $teacher->nip_nik }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $teacher->teacherType?->name ?? '-' }}</td>
                                <td class="px-4 py-3"><x-ui.badge :status="$teacher->status->value" /></td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap items-center justify-end gap-2">
                                        @can('update', $teacher)
                                            <a href="{{ route('teachers.edit', $teacher) }}" class="btn btn-secondary btn-xs">Edit</a>
                                        @endcan
                                        @can('resetPassword', $teacher)
                                            <form method="POST" action="{{ route('teachers.reset-password', $teacher) }}"
                                                  data-confirm="Reset password {{ $teacher->nama }}? Password baru hanya ditampilkan sekali."
                                                  data-confirm-title="Reset password"
                                                  data-confirm-label="Ya, reset"
                                                  data-confirm-tone="warning">
                                                @csrf
                                                <button class="btn btn-secondary btn-xs">Reset Password</button>
                                            </form>
                                        @endcan
                                        @can('delete', $teacher)
                                            <form method="POST" action="{{ route('teachers.destroy', $teacher) }}"
                                                  data-confirm="Hapus {{ $teacher->nama }}? Akun login akan dinonaktifkan (soft delete)."
                                                  data-confirm-title="Hapus data guru"
                                                  data-confirm-label="Ya, hapus"
                                                  data-confirm-tone="danger">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-danger btn-xs">Hapus</button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <x-ui.empty-row :colspan="6" message="Belum ada data guru." />
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($teachers->hasPages())
                <div class="border-t border-gray-100 px-4 py-3">{{ $teachers->links() }}</div>
            @endif
        </x-ui.card>
    </div>
</x-app-layout>
