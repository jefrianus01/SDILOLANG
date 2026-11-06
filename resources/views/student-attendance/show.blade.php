@php
    $recordedCount = $existing->count();
@endphp

<x-app-layout :title="'Absensi '.$classroom->name">
    <div class="space-y-5">
        <a href="{{ route('student-attendance.index', ['date' => $date]) }}" class="text-sm font-semibold text-brand-600 hover:text-brand-700">&larr; Kembali ke daftar kelas</a>

        <x-ui.page-header
            eyebrow="Absensi"
            :title="$classroom->name"
            :description="'Wali kelas: '.($classroom->waliKelas?->nama ?? 'Belum ditunjuk').' · '.($classroom->academicYear?->name ?? 'Tahun ajaran tidak diketahui')">
            <x-slot name="actions">
                <form method="GET" action="{{ route('student-attendance.show', $classroom) }}" class="flex items-end gap-2">
                    <div>
                        <x-input-label for="date" value="Tanggal" />
                        <input id="date" type="date" name="date" value="{{ $date }}" max="{{ now()->toDateString() }}"
                               class="field mt-1">
                    </div>
                    <button class="btn btn-primary">Tampilkan</button>
                </form>
                <a href="{{ route('reports.class', ['classroom' => $classroom, 'month' => \Illuminate\Support\Carbon::parse($date)->format('Y-m')]) }}"
                   class="btn btn-secondary">Rekap Bulanan</a>
            </x-slot>
        </x-ui.page-header>

        @if (! $canEdit)
            <x-ui.alert type="info">
                Anda hanya dapat melihat absensi kelas ini. Pengisian hanya dapat dilakukan oleh wali kelas.
            </x-ui.alert>
        @endif

        <x-ui.card padding="p-0">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 px-4 py-3">
                <p class="text-sm text-gray-600">
                    {{ \Illuminate\Support\Carbon::parse($date)->translatedFormat('l, d F Y') }}
                    &middot; {{ $students->count() }} siswa aktif
                    &middot; <span class="font-semibold">{{ $recordedCount }}</span> sudah tercatat
                </p>
                @if ($canEdit)
                    <div class="flex items-center gap-2 text-xs">
                        <button type="button" data-mark="hadir"
                                class="btn btn-secondary btn-sm">Tandai semua Hadir</button>
                    </div>
                @endif
            </div>

            <form method="POST" action="{{ route('student-attendance.store', $classroom) }}" id="attendance-form">
                @csrf
                <input type="hidden" name="date" value="{{ $date }}">

                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead class="bg-gray-50/80">
                            <tr>
                                <th class="w-12 px-4 py-3">No</th>
                                <th class="px-4 py-3">Nomor Induk Siswa</th>
                                <th class="px-4 py-3">Nama</th>
                                <th class="px-4 py-3">Status Kehadiran</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse ($students as $student)
                                @php($current = $existing->get($student->id)?->status?->value ?? 'hadir')
                                <tr>
                                    <td class="px-4 py-3 text-gray-500">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-3 font-mono text-gray-700">{{ $student->nomor_induk_siswa }}</td>
                                    <td class="px-4 py-3 font-semibold text-gray-900">{{ $student->nama }}</td>
                                    <td class="px-4 py-3">
                                        @if ($canEdit)
                                            <div class="flex flex-wrap gap-3">
                                                @foreach ($statuses as $status)
                                                    <label class="inline-flex items-center gap-1.5 text-sm">
                                                        <input type="radio" name="statuses[{{ $student->id }}]" value="{{ $status->value }}"
                                                               @checked($current === $status->value)
                                                               class="border-gray-300 text-brand-600 focus:ring-brand-500">
                                                        <span>{{ $status->label() }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        @else
                                            <x-ui.badge :status="$current" />
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <x-ui.empty-row :colspan="4" message="Belum ada siswa aktif di kelas ini." />
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($canEdit && $students->isNotEmpty())
                    <div class="flex flex-wrap items-center justify-between gap-3 border-t border-gray-100 px-4 py-3">
                        <p class="text-xs text-gray-500">Menyimpan akan menimpa data absensi tanggal ini jika sudah ada.</p>
                        <button class="btn btn-primary">Simpan Absensi</button>
                    </div>
                @endif
            </form>
        </x-ui.card>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('[data-mark]').forEach(function (button) {
                    button.addEventListener('click', function () {
                        var value = button.getAttribute('data-mark');
                        document.querySelectorAll('#attendance-form input[type="radio"][value="' + value + '"]').forEach(function (radio) {
                            radio.checked = true;
                        });
                    });
                });
            });
        </script>
    @endpush
</x-app-layout>
