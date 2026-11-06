<x-app-layout title="Kenaikan Kelas">
    <div class="mx-auto max-w-4xl space-y-5">
        <a href="{{ route('students.index') }}" class="text-sm font-semibold text-brand-600 hover:text-brand-700">&larr; Kembali ke data siswa</a>

        <x-ui.card title="Kenaikan Kelas / Kelulusan" description="Pindahkan seluruh siswa aktif dari satu kelas ke kelas lain, atau tandai lulus jika kelas tujuan dikosongkan.">
            <form method="POST" action="{{ route('students.promote') }}" class="space-y-4"
                  onsubmit="return confirm('Proses kenaikan kelas akan mengubah data banyak siswa sekaligus. Lanjutkan?')">
                @csrf

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <x-input-label for="source_classroom_id" value="Kelas asal" />
                        <select id="source_classroom_id" name="source_classroom_id" required
                                class="field mt-1">
                            <option value="">Pilih kelas asal</option>
                            @foreach ($classrooms as $classroom)
                                <option value="{{ $classroom->id }}" @selected((string) old('source_classroom_id') === (string) $classroom->id)>
                                    {{ $classroom->name }} — {{ $classroom->academicYear?->name }} ({{ $classroom->students_count }} siswa aktif)
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('source_classroom_id')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="target_classroom_id" value="Kelas tujuan (opsional)" />
                        <select id="target_classroom_id" name="target_classroom_id"
                                class="field mt-1">
                            <option value="">Kosongkan = tandai LULUS</option>
                            @foreach ($classrooms as $classroom)
                                <option value="{{ $classroom->id }}" @selected((string) old('target_classroom_id') === (string) $classroom->id)>
                                    {{ $classroom->name }} — {{ $classroom->academicYear?->name }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('target_classroom_id')" class="mt-2" />
                    </div>
                </div>

                <x-ui.alert type="warning">
                    Tindakan ini hanya dapat dilakukan oleh Super Admin. Pastikan tahun ajaran dan kelas tujuan sudah benar.
                </x-ui.alert>

                <button class="btn btn-primary">Proses Kenaikan Kelas</button>
            </form>
        </x-ui.card>

        <x-ui.card title="Ringkasan Kelas" padding="p-0">
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead class="bg-gray-50/80">
                        <tr>
                            <th class="px-4 py-3">Kelas</th>
                            <th class="px-4 py-3">Tahun Ajaran</th>
                            <th class="px-4 py-3 text-right">Siswa Aktif</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($classrooms as $classroom)
                            <tr>
                                <td class="px-4 py-3 font-semibold text-gray-900">{{ $classroom->name }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $classroom->academicYear?->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-right text-gray-700">{{ $classroom->students_count }}</td>
                            </tr>
                        @empty
                            <x-ui.empty-row :colspan="3" message="Belum ada kelas." />
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-ui.card>
    </div>
</x-app-layout>
