@php
    $statusColumns = App\Enums\TeacherAttendanceStatus::cases();
@endphp

<x-app-layout title="Rekap Guru">
    <div class="space-y-5">
        <x-ui.page-header
            eyebrow="Laporan"
            title="Rekap Absensi Guru"
            :description="$month->translatedFormat('F Y').' · hanya guru berstatus aktif'">
            <x-slot name="actions">
                <form method="GET" action="{{ route('reports.teacher') }}" class="flex items-end gap-2">
                    <div>
                        <x-input-label for="month" value="Bulan" />
                        <input id="month" type="month" name="month" value="{{ $month->format('Y-m') }}"
                               class="field mt-1">
                    </div>
                    <button class="btn btn-primary">Tampilkan</button>
                </form>
                <a href="{{ route('reports.teacher.pdf', ['month' => $month->format('Y-m')]) }}"
                   class="btn btn-danger">Export PDF</a>
                <a href="{{ route('reports.teacher.excel', ['month' => $month->format('Y-m')]) }}"
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

        <x-ui.card title="Rekap per Guru" :description="'Jumlah hari tercatat dalam bulan '.$month->translatedFormat('F Y')" padding="p-0">
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead class="bg-gray-50/80">
                        <tr>
                            <th class="px-4 py-3">No</th>
                            <th class="px-4 py-3">Nama Guru</th>
                            <th class="px-4 py-3">Jenis Guru</th>
                            @foreach ($statusColumns as $status)
                                <th class="px-3 py-3 text-center">{{ $status->label() }}</th>
                            @endforeach
                            <th class="px-3 py-3 text-center">Menunggu</th>
                            <th class="px-3 py-3 text-center">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($recap['rows'] as $row)
                            <tr>
                                <td class="px-4 py-3 text-gray-500">{{ $row['no'] }}</td>
                                <td class="px-4 py-3">
                                    <p class="font-semibold text-gray-900">{{ $row['teacher']->nama }}</p>
                                    <p class="font-mono text-xs text-gray-500">{{ $row['teacher']->nip_nik }}</p>
                                </td>
                                <td class="px-4 py-3 text-gray-600">{{ $row['teacher']->teacherType?->name ?? '-' }}</td>
                                @foreach ($statusColumns as $status)
                                    <td class="px-3 py-3 text-center text-gray-700">{{ $row['counts'][$status->value] ?? 0 }}</td>
                                @endforeach
                                <td class="px-3 py-3 text-center">
                                    @if ($row['pending'] > 0)
                                        <span class="rounded-full bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-700">{{ $row['pending'] }}</span>
                                    @else
                                        <span class="text-gray-400">0</span>
                                    @endif
                                </td>
                                <td class="px-3 py-3 text-center font-semibold text-gray-900">{{ $row['recorded'] }}</td>
                            </tr>
                        @empty
                            <x-ui.empty-row :colspan="count($statusColumns) + 5" message="Belum ada guru aktif." />
                        @endforelse
                    </tbody>
                    @if (! empty($recap['rows']))
                        <tfoot class="bg-gray-50 font-semibold text-gray-800">
                            <tr>
                                <td class="px-4 py-3" colspan="3">Total</td>
                                @foreach ($statusColumns as $status)
                                    <td class="px-3 py-3 text-center">{{ $recap['totals'][$status->value] ?? 0 }}</td>
                                @endforeach
                                <td class="px-3 py-3 text-center">{{ $recap['totals']['pending'] ?? 0 }}</td>
                                <td class="px-3 py-3 text-center">
                                    {{ collect($recap['totals'])->except('pending')->sum() }}
                                </td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </x-ui.card>
    </div>
</x-app-layout>
