<x-app-layout title="Atur Wali Kelas">
    <div class="mx-auto max-w-2xl space-y-5">
        <a href="{{ route('classrooms.index', ['academic_year_id' => $classroom->academic_year_id]) }}" class="text-sm font-semibold text-brand-600 hover:text-brand-700">&larr; Kembali ke daftar kelas</a>

        <x-ui.card :title="'Wali Kelas '.$classroom->name" :description="$classroom->academicYear?->name">
            <form method="POST" action="{{ route('classrooms.update', $classroom) }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <x-input-label for="wali_kelas_id" value="Guru" />
                    <select id="wali_kelas_id" name="wali_kelas_id" class="field mt-1">
                        <option value="">— Tidak ada / kosongkan —</option>
                        @foreach ($teachers as $teacher)
                            <option value="{{ $teacher->id }}"
                                    @selected((string) old('wali_kelas_id', $classroom->wali_kelas_id) === (string) $teacher->id)
                                    @disabled(in_array($teacher->id, $assignedElsewhere, true) && $teacher->id !== $classroom->wali_kelas_id)>
                                {{ $teacher->nama }} — {{ $teacher->nip_nik }} ({{ $teacher->teacherType?->name }})
                                @if (in_array($teacher->id, $assignedElsewhere, true))
                                    [sudah menjadi wali kelas lain]
                                @endif
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500">Satu guru hanya dapat menjadi wali kelas satu kelas pada tahun ajaran yang sama.</p>
                    <x-input-error :messages="$errors->get('wali_kelas_id')" class="mt-2" />
                </div>

                <x-ui.alert type="info">
                    Menunjuk guru sebagai wali kelas otomatis memberikan role <strong>Wali Kelas</strong>.
                    Jika diganti, role tersebut dilepas dari guru sebelumnya.
                </x-ui.alert>

                <div class="flex items-center justify-end gap-2">
                    <a href="{{ route('classrooms.index', ['academic_year_id' => $classroom->academic_year_id]) }}" class="btn btn-secondary">Batal</a>
                    <button class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </x-ui.card>
    </div>
</x-app-layout>
