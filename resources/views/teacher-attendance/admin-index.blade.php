@php
    $approvalStatuses = App\Enums\ApprovalStatus::cases();
@endphp

<x-app-layout title="Absensi Guru">
    <div class="space-y-5">
        <x-ui.page-header
            eyebrow="Absensi"
            title="Absensi Guru"
            description="Rekap absensi seluruh guru berdasarkan rentang tanggal.">
            <x-slot name="actions">
                @can('approve', App\Models\TeacherAttendance::class)
                    <a href="{{ route('teacher-attendance.approvals') }}" class="btn btn-primary">
                        Persetujuan Izin @if ($pendingCount > 0)<span class="ml-1 rounded-full bg-white/25 px-2 py-0.5 text-xs">{{ $pendingCount }}</span>@endif
                    </a>
                @endcan
                <a href="{{ route('reports.teacher') }}" class="btn btn-secondary">Rekap Bulanan</a>
            </x-slot>
        </x-ui.page-header>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ($statuses as $status)
                <x-ui.stat
                    :label="$status->label()"
                    :value="$summary[$status->value] ?? 0"
                    hint="Periode"
                    :tone="$status->value === 'hadir' ? 'emerald' : ($status->value === 'alpa' ? 'rose' : 'sky')"
                />
            @endforeach
        </div>

        <x-ui.card padding="p-4">
            <form method="GET" action="{{ route('teacher-attendance.admin') }}" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-6">
                <div>
                    <x-input-label for="from" value="Dari tanggal" />
                    <input id="from" type="date" name="from" value="{{ $filters['from'] }}"
                           class="field mt-1">
                </div>
                <div>
                    <x-input-label for="to" value="Sampai tanggal" />
                    <input id="to" type="date" name="to" value="{{ $filters['to'] }}"
                           class="field mt-1">
                </div>
                <div>
                    <x-input-label for="teacher_id" value="Guru" />
                    <select id="teacher_id" name="teacher_id" class="field mt-1">
                        <option value="">Semua guru</option>
                        @foreach ($teachers as $teacher)
                            <option value="{{ $teacher->id }}" @selected((string) ($filters['teacher_id'] ?? '') === (string) $teacher->id)>{{ $teacher->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="status" value="Status" />
                    <select id="status" name="status" class="field mt-1">
                        <option value="">Semua status</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status->value }}" @selected(($filters['status'] ?? '') === $status->value)>{{ $status->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="approval_status" value="Persetujuan" />
                    <select id="approval_status" name="approval_status" class="field mt-1">
                        <option value="">Semua</option>
                        @foreach ($approvalStatuses as $approval)
                            <option value="{{ $approval->value }}" @selected(($filters['approval_status'] ?? '') === $approval->value)>{{ $approval->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button class="btn btn-primary">Filter</button>
                    <a href="{{ route('teacher-attendance.admin') }}" class="btn btn-secondary btn-sm">Reset</a>
                </div>
            </form>
        </x-ui.card>

        <x-ui.card padding="p-0">
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead class="bg-gray-50/80">
                        <tr>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">Guru</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Masuk</th>
                            <th class="px-4 py-3">Pulang</th>
                            <th class="px-4 py-3">Jarak</th>
                            <th class="px-4 py-3">Persetujuan</th>
                            <th class="px-4 py-3">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($attendances as $attendance)
                            <tr>
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $attendance->date->translatedFormat('d M Y') }}</td>
                                <td class="px-4 py-3">
                                    <p class="font-semibold text-gray-900">{{ $attendance->teacher?->nama ?? '-' }}</p>
                                    <p class="text-xs text-gray-500">{{ $attendance->teacher?->teacherType?->name ?? '-' }}</p>
                                </td>
                                <td class="px-4 py-3"><x-ui.badge :status="$attendance->status->value" /></td>
                                <td class="px-4 py-3 text-gray-700">{{ $attendance->check_in_at?->format('H:i') ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $attendance->check_out_at?->format('H:i') ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $attendance->check_in_distance !== null ? $attendance->check_in_distance.' m' : '-' }}</td>
                                <td class="px-4 py-3">
                                    <x-ui.badge :status="$attendance->approval_status->value" />
                                    @if ($attendance->approver)
                                        <p class="mt-1 text-xs text-gray-500">{{ $attendance->approver->name }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-600">
                                    {{ $attendance->reason ?? $attendance->approval_note ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <x-ui.empty-row :colspan="8" message="Tidak ada data absensi pada periode ini." />
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($attendances->hasPages())
                <div class="border-t border-gray-100 px-4 py-3">{{ $attendances->links() }}</div>
            @endif
        </x-ui.card>
    </div>
</x-app-layout>
