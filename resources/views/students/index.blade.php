@php
    $canCreate = auth()->user()->can('create', App\Models\Student::class);
    $readOnly = ! $canCreate;
@endphp

<x-app-layout title="Data Siswa">
    <div class="space-y-5">
        <x-ui.page-header eyebrow="Data" title="Data Siswa">
            <x-slot name="actions">
                @can('create', App\Models\Student::class)
                    <a href="{{ route('students.create') }}" class="btn btn-primary">+ Tambah Siswa</a>
                    <a href="{{ route('students.import.form') }}" class="btn btn-secondary">Import Excel</a>
                @endcan
                @can('promote', App\Models\Student::class)
                    <a href="{{ route('students.promote.form') }}" class="btn btn-secondary">Kenaikan Kelas</a>
                @endcan
                <a href="{{ route('students.export.excel', request()->query()) }}" class="btn btn-secondary">Export Excel</a>
                <a href="{{ route('students.export.pdf', request()->query()) }}" class="btn btn-danger">Export PDF</a>
            </x-slot>

            <p class="mt-1.5 max-w-2xl text-sm leading-6 text-gray-500">
                @if ($readOnly)
                    Anda dapat melihat data siswa, namun hanya wali kelas yang boleh menambah atau mengubah data.
                @else
                    Data siswa dikelola oleh wali kelas masing-masing. Hanya NIS, nama, dan kelas yang diisi.
                @endif
            </p>
            @if ($homeroom)
                <p class="mt-1 text-xs text-gray-500">Kelas Anda: <span class="font-semibold text-gray-700">{{ $homeroom->name }}</span></p>
            @endif
        </x-ui.page-header>

        <x-ui.card padding="p-4">
            <form method="GET" action="{{ route('students.index') }}" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                <div class="lg:col-span-2">
                    <x-input-label for="search" value="Cari nama / NIS" />
                    <x-text-input id="search" name="search" :value="$filters['search'] ?? ''" class="mt-1 block w-full" placeholder="Ketik nama atau nomor induk siswa" />
                </div>
                <div>
                    <x-input-label for="classroom_id" value="Kelas" />
                    <select id="classroom_id" name="classroom_id" class="field mt-1">
                        <option value="">Semua kelas</option>
                        @foreach ($classrooms as $classroom)
                            <option value="{{ $classroom->id }}" @selected((string) ($filters['classroom_id'] ?? '') === (string) $classroom->id)>{{ $classroom->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="status" value="Status" />
                    <div class="mt-1 flex gap-2">
                        <select id="status" name="status" class="field">
                            <option value="">Semua</option>
                            @foreach ($statuses as $status)
                                <option value="{{ $status->value }}" @selected(($filters['status'] ?? '') === $status->value)>{{ ucfirst($status->value) }}</option>
                            @endforeach
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
                            <th class="w-12 px-4 py-3">No</th>
                            <th class="px-4 py-3">Nomor Induk Siswa</th>
                            <th class="px-4 py-3">Nama</th>
                            <th class="px-4 py-3">Kelas</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($students as $student)
                            <tr>
                                <td class="px-4 py-3 text-gray-500">{{ $students->firstItem() + $loop->index }}</td>
                                <td class="px-4 py-3 font-mono text-gray-700">{{ $student->nomor_induk_siswa }}</td>
                                <td class="px-4 py-3 font-semibold text-gray-900">{{ $student->nama }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $student->classroom?->name ?? '-' }}</td>
                                <td class="px-4 py-3"><x-ui.badge :status="$student->status->value" /></td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap items-center justify-end gap-2">
                                        @can('update', $student)
                                            <a href="{{ route('students.edit', $student) }}" class="btn btn-secondary btn-xs">Edit</a>
                                        @endcan
                                        @can('delete', $student)
                                            <form method="POST" action="{{ route('students.destroy', $student) }}"
                                                  onsubmit="return confirm('Hapus {{ $student->nama }}? Data akan dihapus (soft delete).')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-danger btn-xs">Hapus</button>
                                            </form>
                                        @endcan
                                        @if ($readOnly && ! auth()->user()->can('update', $student))
                                            <span class="text-xs text-gray-400">Hanya lihat</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <x-ui.empty-row :colspan="6" message="Belum ada data siswa." />
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($students->hasPages())
                <div class="border-t border-gray-100 px-4 py-3">{{ $students->links() }}</div>
            @endif
        </x-ui.card>
    </div>
</x-app-layout>
