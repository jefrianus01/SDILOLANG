@php
    $classroom = $recap['classroom'];
    $statusColumns = App\Enums\AttendanceStatus::cases();
@endphp

<x-app-layout :title="'Rekap '.$classroom->name">
    <div class="space-y-5">
        <a href="{{ route('classrooms.index') }}" class="text-sm font-semibold text-brand-600 hover:text-brand-700">&larr; Kembali ke daftar kelas</a>

        <x-ui.page-header
            eyebrow="Laporan"
            :title="'Rekap Absensi '.$classroom->name"
            :description="$month->translatedFormat('F Y').' · Wali kelas: '.($classroom->waliKelas?->nama ?? 'Belum ditunjuk')">
            <x-slot name="actions">
                <form method="GET" action="{{ route('reports.class', $classroom) }}" class="flex items-end gap-2">
                    <div>
                        <x-input-label for="month" value="Bulan" />
                        <input id="month" type="month" name="month" value="{{ $month->format('Y-m') }}"
                               class="field mt-1">
                    </div>
                    <button class="btn btn-primary">Tampilkan</button>
                </form>
                <a href="{{ route('reports.class.pdf', ['classroom' => $classroom, 'month' => $month->format('Y-m')]) }}"
                   class="btn btn-danger">Export PDF</a>
                <a href="{{ route('reports.class.excel', ['classroom' => $classroom, 'month' => $month->format('Y-m')]) }}"
                   class="btn btn-secondary">Export Excel</a>
            </x-slot>
        </x-ui.page-header>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ($statusColumns as $status)
                <x-ui.stat
                    :label="$status->label()"
                    :value="$recap['totals'][$status->value] ?? 0"
                    hint="Bulan ini"
                    :tone="$status->value === 'hadir' ? 'emerald' : ($status->value === 'alpa' ? 'rose' : 'sky')"
                />
            @endforeach
        </div>

        <x-ui.card title="Detail Harian" :description="'Total catatan: '.$recap['recorded'].' entri'" padding="p-0">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-xs">
                    <thead class="bg-gray-50 text-left font-semibold uppercase tracking-wider text-gray-500">
                        <tr>
                            <th class="sticky left-0 z-10 bg-gray-50 px-3 py-2">No</th>
                            <th class="sticky left-8 z-10 bg-gray-50 px-3 py-2">Nama</th>
                            @foreach ($recap['days'] as $day)
                                <th class="px-1.5 py-2 text-center">{{ $day }}</th>
                            @endforeach
                            @foreach ($statusColumns as $status)
                                <th class="px-2 py-2 text-center">{{ substr($status->label(), 0, 1) }}</th>
                            @endforeach
                            <th class="px-2 py-2 text-center">%</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($recap['rows'] as $row)
                            <tr>
                                <td class="sticky left-0 z-10 bg-white px-3 py-2 text-gray-500">{{ $row['no'] }}</td>
                                <td class="sticky left-8 z-10 bg-white px-3 py-2">
                                    <p class="whitespace-nowrap font-semibold text-gray-900">{{ $row['student']->nama }}</p>
                                    <p class="font-mono text-[11px] text-gray-500">{{ $row['student']->nomor_induk_siswa }}</p>
                                </td>
                                @foreach ($recap['days'] as $day)
                                    @php($status = $row['daily'][$day] ?? null)
                                    <td class="px-1.5 py-2 text-center">
                                        @if ($status)
                                            <span class="inline-block rounded px-1 py-0.5 font-semibold {{ \App\Enums\AttendanceStatus::from($status)->badge() }}">
                                                {{ strtoupper(substr($status, 0, 1)) }}
                                            </span>
                                        @else
                                            <span class="text-gray-300">&middot;</span>
                                        @endif
                                    </td>
                                @endforeach
                                @foreach ($statusColumns as $status)
                                    <td class="px-2 py-2 text-center font-semibold text-gray-700">{{ $row['counts'][$status->value] ?? 0 }}</td>
                                @endforeach
                                <td class="px-2 py-2 text-center font-semibold text-gray-900">{{ number_format($row['percentage'], 1, ',', '.') }}%</td>
                            </tr>
                        @empty
                            <x-ui.empty-row :colspan="count($recap['days']) + 6" message="Belum ada siswa aktif di kelas ini." />
                        @endforelse
                    </tbody>
                    @if (! empty($recap['rows']))
                        <tfoot class="bg-gray-50 font-semibold text-gray-800">
                            <tr>
                                <td class="sticky left-0 z-10 bg-gray-50 px-3 py-2" colspan="2">Total</td>
                                @foreach ($recap['days'] as $day)
                                    <td class="px-1.5 py-2"></td>
                                @endforeach
                                @foreach ($statusColumns as $status)
                                    <td class="px-2 py-2 text-center">{{ $recap['totals'][$status->value] ?? 0 }}</td>
                                @endforeach
                                <td class="px-2 py-2 text-center">{{ $recap['recorded'] }}</td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </x-ui.card>
    </div>
</x-app-layout>
