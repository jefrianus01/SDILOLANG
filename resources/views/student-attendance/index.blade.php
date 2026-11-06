<x-app-layout title="Absensi Siswa">
    <div class="space-y-5">
        <x-ui.page-header
            eyebrow="Absensi"
            title="Absensi Siswa"
            :description="'Pilih kelas untuk mengisi atau melihat absensi harian.'.($academicYear ? ' Tahun ajaran aktif: '.$academicYear->name.'.' : '')">
            <x-slot name="actions">
                <form method="GET" action="{{ route('student-attendance.index') }}" class="flex items-end gap-2">
                    <div>
                        <x-input-label for="date" value="Tanggal" />
                        <input id="date" type="date" name="date" value="{{ $date }}" max="{{ now()->toDateString() }}"
                               class="field mt-1">
                    </div>
                    <button class="btn btn-primary">Tampilkan</button>
                </form>
            </x-slot>
        </x-ui.page-header>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @forelse ($classrooms as $classroom)
                <x-ui.card>
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-lg font-bold text-gray-900">{{ $classroom->name }}</p>
                            <p class="text-sm text-gray-500">Wali kelas: {{ $classroom->waliKelas?->nama ?? 'Belum ditunjuk' }}</p>
                        </div>
                        <span class="rounded-lg bg-brand-50 px-2.5 py-1 text-xs font-semibold text-brand-700">{{ $classroom->students_count }} siswa</span>
                    </div>

                    <div class="mt-4 flex items-center justify-between gap-3 text-sm">
                        <span class="text-gray-500">
                            Tercatat {{ $date }}:
                            <span class="font-semibold text-gray-900">{{ $recorded[$classroom->id] ?? 0 }}</span>
                        </span>
                        @if (($recorded[$classroom->id] ?? 0) > 0)
                            <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-700">Sudah diisi</span>
                        @else
                            <span class="rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-semibold text-amber-700">Belum diisi</span>
                        @endif
                    </div>

                    <div class="mt-4 flex flex-wrap gap-2">
                        <a href="{{ route('student-attendance.show', ['classroom' => $classroom, 'date' => $date]) }}"
                           class="btn btn-primary">
                            Isi / Lihat Absensi
                        </a>
                        @can('view', $classroom)
                            <a href="{{ route('reports.class', ['classroom' => $classroom, 'month' => \Illuminate\Support\Carbon::parse($date)->format('Y-m')]) }}"
                               class="btn btn-secondary">Rekap</a>
                        @endcan
                    </div>
                </x-ui.card>
            @empty
                <x-ui.card class="sm:col-span-2 xl:col-span-3">
                    <p class="py-6 text-center text-sm text-gray-500">Belum ada kelas yang dapat diakses.</p>
                </x-ui.card>
            @endforelse
        </div>
    </div>
</x-app-layout>
