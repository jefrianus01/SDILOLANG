<x-app-layout title="Kelas 1–6">
    <div class="space-y-5">
        <x-ui.card padding="p-4">
            <form method="GET" action="{{ route('classrooms.index') }}" class="flex flex-wrap items-end gap-3">
                <div>
                    <x-input-label for="academic_year_id" value="Tahun ajaran" />
                    <select id="academic_year_id" name="academic_year_id" class="field mt-1">
                        @foreach ($years as $year)
                            <option value="{{ $year->id }}" @selected($selectedYear?->id === $year->id)>{{ $year->name }}{{ $year->is_active ? ' (aktif)' : '' }}</option>
                        @endforeach
                    </select>
                </div>
                <button class="btn btn-primary">Tampilkan</button>
            </form>
        </x-ui.card>

        <x-ui.card title="Daftar Kelas" :description="$selectedYear ? 'Tahun ajaran '.$selectedYear->name : 'Belum ada tahun ajaran'" padding="p-0">
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead class="bg-gray-50/80">
                        <tr>
                            <th class="px-4 py-3">Kelas</th>
                            <th class="px-4 py-3">Wali Kelas</th>
                            <th class="px-4 py-3 text-right">Siswa Aktif</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($classrooms as $classroom)
                            <tr>
                                <td class="px-4 py-3 font-semibold text-gray-900">{{ $classroom->name }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $classroom->waliKelas?->nama ?? 'Belum ditunjuk' }}</td>
                                <td class="px-4 py-3 text-right text-gray-700">{{ $classroom->students_count }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap items-center justify-end gap-2">
                                        @can('assignHomeroom', App\Models\Classroom::class)
                                            <a href="{{ route('classrooms.edit', $classroom) }}" class="btn btn-secondary btn-xs">Atur Wali Kelas</a>
                                        @endcan
                                        @can('view', $classroom)
                                            <a href="{{ route('reports.class', $classroom) }}" class="btn btn-secondary btn-xs">Rekap</a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <x-ui.empty-row :colspan="4" message="Belum ada kelas untuk tahun ajaran ini." />
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-ui.card>
    </div>
</x-app-layout>
