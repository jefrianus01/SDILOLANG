@php
    $user = auth()->user();
@endphp

<x-app-layout title="Dashboard">
    @if ($mode === 'admin')
        <div class="space-y-6">
            <x-ui.page-header
                eyebrow="Ringkasan"
                title="Dashboard"
                :description="'Tahun ajaran '.($academicYear?->name ?? 'belum diatur').' · '.\Illuminate\Support\Carbon::parse($today)->translatedFormat('l, d F Y')"
            >
                <x-slot name="actions">
                    @if ($pendingApprovals > 0)
                        <a href="{{ route('teacher-attendance.approvals') }}" class="btn btn-secondary">
                            <x-ui.icon name="shield-check" class="h-4 w-4 text-amber-600" />
                            Persetujuan Izin
                            <span class="ml-0.5 rounded-full bg-amber-100 px-1.5 py-0.5 text-2xs font-bold text-amber-700">{{ $pendingApprovals }}</span>
                        </a>
                    @endif
                    <a href="{{ route('reports.teacher') }}" class="btn btn-primary">
                        Lihat rekap guru
                    </a>
                </x-slot>
            </x-ui.page-header>

            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <x-ui.stat label="Guru Aktif" :value="$totalTeachers" hint="Terdaftar pada tahun ajaran aktif" tone="brand" />
                <x-ui.stat label="Siswa Aktif" :value="$totalStudents" hint="Seluruh kelas" tone="emerald" />
                <x-ui.stat label="Rombongan Belajar" :value="$classrooms->count()" hint="Kelas 1–6" tone="sky" />
                <x-ui.stat label="Menunggu Persetujuan" :value="$pendingApprovals" hint="Pengajuan izin guru" tone="amber" />
            </div>

            <div class="grid gap-6 xl:grid-cols-3">
                <x-ui.card title="Rekap kelas" description="Jumlah siswa aktif per kelas" class="xl:col-span-2" padding="p-0">
                    <div class="overflow-x-auto">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Kelas</th>
                                    <th>Wali Kelas</th>
                                    <th class="text-right">Siswa</th>
                                    <th class="text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($classrooms as $classroom)
                                    <tr>
                                        <td class="font-semibold text-gray-900">{{ $classroom->name }}</td>
                                        <td>{{ $classroom->waliKelas?->nama ?? 'Belum ditunjuk' }}</td>
                                        <td class="text-right font-medium tabular-nums text-gray-800">{{ $classroom->students_count }}</td>
                                        <td class="text-right">
                                            <a href="{{ route('reports.class', $classroom) }}"
                                               class="inline-flex items-center gap-1 text-[13px] font-semibold text-brand-700 hover:text-brand-900">
                                                Rekap
                                                <x-ui.icon name="chevron-right" class="h-3.5 w-3.5" />
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <x-ui.empty-row :colspan="4" message="Belum ada tahun ajaran aktif." description="Aktifkan tahun ajaran agar data kelas tampil." />
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </x-ui.card>

                <div class="space-y-6">
                    <x-ui.card title="Absensi guru hari ini" description="Rekap status kehadiran">
                        @php($statusTotal = max(1, $teacherStatusCounts->sum()))

                        <div class="space-y-3.5">
                            @foreach ($statuses as $status)
                                @php($count = $teacherStatusCounts[$status->value] ?? 0)
                                @php($percent = round($count / $statusTotal * 100))

                                <div>
                                    <div class="flex items-center justify-between gap-3 text-[13px]">
                                        <span class="text-gray-600">{{ $status->label() }}</span>
                                        <span class="font-semibold tabular-nums text-gray-900">{{ $count }}</span>
                                    </div>
                                    <div class="mt-1.5 h-1.5 overflow-hidden rounded-full bg-gray-100">
                                        <div class="h-full rounded-full {{ $status->value === 'hadir' ? 'bg-emerald-500' : ($status->value === 'alpa' ? 'bg-rose-500' : 'bg-brand-400') }}"
                                             style="width: {{ $percent }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-5 border-t border-gray-100 pt-3">
                            <a href="{{ route('teacher-attendance.admin') }}" class="link-arrow text-brand-700 transition-colors hover:text-brand-900">
                                Detail absensi guru
                                <x-ui.icon name="arrow-right" class="h-3.5 w-3.5" />
                            </a>
                        </div>
                    </x-ui.card>

                    <x-ui.card title="Pengajuan terbaru" padding="p-0">
                        <div class="divide-y divide-gray-100">
                            @forelse ($pendingRequests as $requestItem)
                                <div class="flex items-start justify-between gap-3 px-5 py-3">
                                    <div class="min-w-0">
                                        <p class="truncate text-[13px] font-semibold text-gray-900">{{ $requestItem->teacher?->nama }}</p>
                                        <p class="mt-0.5 text-xs text-gray-500">
                                            {{ $requestItem->status->label() }} &middot; {{ $requestItem->date->translatedFormat('d M Y') }}
                                        </p>
                                    </div>
                                    <x-ui.badge :status="$requestItem->approval_status->value" />
                                </div>
                            @empty
                                <p class="px-5 py-8 text-center text-[13px] text-gray-500">Tidak ada pengajuan menunggu.</p>
                            @endforelse
                        </div>

                        @if ($pendingApprovals > 0)
                            <div class="border-t border-gray-100 px-5 py-3">
                                <a href="{{ route('teacher-attendance.approvals') }}" class="link-arrow text-brand-700 transition-colors hover:text-brand-900">
                                    Lihat semua pengajuan
                                    <x-ui.icon name="arrow-right" class="h-3.5 w-3.5" />
                                </a>
                            </div>
                        @endif
                    </x-ui.card>
                </div>
            </div>

            <x-ui.card title="Berita terbaru" description="Konten yang tampil di website sekolah" padding="p-0">
                <div class="divide-y divide-gray-100">
                    @forelse ($recentNews as $item)
                        <div class="flex items-center justify-between gap-4 px-5 py-3">
                            <div class="min-w-0">
                                <p class="truncate text-[13px] font-semibold text-gray-900">{{ $item->title }}</p>
                                <p class="mt-0.5 text-xs text-gray-500">{{ $item->created_at?->translatedFormat('d M Y') }}</p>
                            </div>
                            <x-ui.badge :status="$item->is_published ? 'aktif' : 'nonaktif'" />
                        </div>
                    @empty
                        <p class="px-5 py-8 text-center text-[13px] text-gray-500">Belum ada berita.</p>
                    @endforelse
                </div>

                <div class="border-t border-gray-100 px-5 py-3">
                    <a href="{{ route('admin.news.index') }}" class="link-arrow text-brand-700 transition-colors hover:text-brand-900">
                        Kelola berita
                        <x-ui.icon name="arrow-right" class="h-3.5 w-3.5" />
                    </a>
                </div>
            </x-ui.card>
        </div>
    @elseif ($mode === 'homeroom')
        <div class="space-y-6">
            <x-ui.page-header
                eyebrow="Wali Kelas"
                :title="$classroom?->name ?? 'Belum ada kelas'"
                :description="$classroom
                    ? $students->count().' siswa aktif · '.\Illuminate\Support\Carbon::parse($today)->translatedFormat('l, d F Y')
                    : 'Anda belum ditetapkan sebagai wali kelas pada tahun ajaran aktif.'"
            >
                <x-slot name="actions">
                    @if ($classroom)
                        <a href="{{ route('student-attendance.show', $classroom) }}" class="btn btn-primary">
                            <x-ui.icon name="clipboard" class="h-4 w-4" />
                            Isi absensi hari ini
                        </a>
                        <a href="{{ route('reports.class', $classroom) }}" class="btn btn-secondary">Rekap bulanan</a>
                    @endif
                </x-slot>
            </x-ui.page-header>

            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                @foreach ($statuses as $status)
                    <x-ui.stat
                        :label="$status->label()"
                        :value="$todayCounts[$status->value] ?? 0"
                        hint="Absensi hari ini"
                        :tone="$status->value === 'hadir' ? 'emerald' : ($status->value === 'alpa' ? 'rose' : ($status->value === 'sakit' ? 'amber' : 'sky'))"
                    />
                @endforeach
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <x-ui.card title="Absensi saya hari ini" class="lg:col-span-1">
                    @if ($teacherAttendance)
                        <dl class="space-y-3 text-[13px]">
                            <div class="flex items-center justify-between gap-3">
                                <dt class="text-gray-500">Status</dt>
                                <dd><x-ui.badge :status="$teacherAttendance->status->value" /></dd>
                            </div>
                            <div class="flex items-center justify-between gap-3">
                                <dt class="text-gray-500">Masuk</dt>
                                <dd class="font-semibold tabular-nums text-gray-900">{{ $teacherAttendance->check_in_at?->format('H:i') ?? '-' }}</dd>
                            </div>
                            <div class="flex items-center justify-between gap-3">
                                <dt class="text-gray-500">Pulang</dt>
                                <dd class="font-semibold tabular-nums text-gray-900">{{ $teacherAttendance->check_out_at?->format('H:i') ?? '-' }}</dd>
                            </div>
                        </dl>
                    @else
                        <p class="text-[13px] leading-6 text-gray-500">Anda belum melakukan absensi hari ini.</p>
                    @endif

                    <div class="mt-4 border-t border-gray-100 pt-3">
                        <a href="{{ route('teacher-attendance.index') }}" class="link-arrow text-brand-700 transition-colors hover:text-brand-900">
                            Buka halaman absensi
                            <x-ui.icon name="arrow-right" class="h-3.5 w-3.5" />
                        </a>
                    </div>
                </x-ui.card>

                <x-ui.card title="Tren 7 hari terakhir" description="Jumlah siswa per status" class="lg:col-span-2" padding="p-0">
                    <div class="overflow-x-auto">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th class="text-right">Hadir</th>
                                    <th class="text-right">Sakit</th>
                                    <th class="text-right">Izin</th>
                                    <th class="text-right">Alpa</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($weekTrend as $date => $rows)
                                    <tr>
                                        <td class="font-medium text-gray-900">{{ \Illuminate\Support\Carbon::parse($date)->translatedFormat('D, d M') }}</td>
                                        @foreach (['hadir', 'sakit', 'izin', 'alpa'] as $status)
                                            <td class="text-right tabular-nums">
                                                {{ optional($rows->firstWhere('status', $status))['total'] ?? 0 }}
                                            </td>
                                        @endforeach
                                    </tr>
                                @empty
                                    <x-ui.empty-row :colspan="5" message="Belum ada data absensi pekan ini." />
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </x-ui.card>
            </div>
        </div>
    @else
        <div class="space-y-6">
            <x-ui.page-header
                eyebrow="Absensi Guru"
                :title="$user->name"
                :description="($teacher?->teacherType?->name ?? 'Guru').' · NIP/NIK '.($teacher?->nip_nik ?? '-')"
            >
                <x-slot name="actions">
                    <a href="{{ route('teacher-attendance.index') }}" class="btn btn-primary">
                        <x-ui.icon name="map-pin" class="h-4 w-4" />
                        Buka absensi saya
                    </a>
                </x-slot>
            </x-ui.page-header>

            <div class="grid gap-6 lg:grid-cols-2">
                <x-ui.card title="Absensi hari ini" description="Status kehadiran Anda hari ini">
                    @if ($teacherAttendance)
                        <dl class="grid grid-cols-2 gap-3 text-[13px]">
                            <div class="rounded-lg border border-gray-200 bg-gray-50/60 p-3.5">
                                <dt class="text-gray-500">Status</dt>
                                <dd class="mt-1.5"><x-ui.badge :status="$teacherAttendance->status->value" /></dd>
                            </div>
                            <div class="rounded-lg border border-gray-200 bg-gray-50/60 p-3.5">
                                <dt class="text-gray-500">Masuk</dt>
                                <dd class="mt-1.5 font-semibold tabular-nums text-gray-900">{{ $teacherAttendance->check_in_at?->format('H:i') ?? '-' }}</dd>
                            </div>
                            <div class="rounded-lg border border-gray-200 bg-gray-50/60 p-3.5">
                                <dt class="text-gray-500">Pulang</dt>
                                <dd class="mt-1.5 font-semibold tabular-nums text-gray-900">{{ $teacherAttendance->check_out_at?->format('H:i') ?? '-' }}</dd>
                            </div>
                            <div class="rounded-lg border border-gray-200 bg-gray-50/60 p-3.5">
                                <dt class="text-gray-500">Jarak</dt>
                                <dd class="mt-1.5 font-semibold tabular-nums text-gray-900">
                                    {{ $teacherAttendance->check_in_distance !== null ? $teacherAttendance->check_in_distance.' m' : '-' }}
                                </dd>
                            </div>
                        </dl>
                    @else
                        <p class="text-[13px] leading-6 text-gray-500">
                            Anda belum melakukan absensi hari ini. Absensi hanya dapat dilakukan di dalam area sekolah.
                        </p>
                    @endif
                </x-ui.card>

                <x-ui.card title="Riwayat terakhir" description="10 absensi terakhir" padding="p-0">
                    <div class="divide-y divide-gray-100">
                        @forelse ($history as $row)
                            <div class="flex items-center justify-between gap-3 px-5 py-3">
                                <div class="min-w-0">
                                    <p class="text-[13px] font-medium text-gray-900">{{ $row->date->translatedFormat('d M Y') }}</p>
                                    <p class="mt-0.5 text-xs tabular-nums text-gray-500">
                                        Masuk {{ $row->check_in_at?->format('H:i') ?? '-' }} &middot; Pulang {{ $row->check_out_at?->format('H:i') ?? '-' }}
                                    </p>
                                </div>
                                <x-ui.badge :status="$row->status->value" />
                            </div>
                        @empty
                            <p class="px-5 py-8 text-center text-[13px] text-gray-500">Belum ada riwayat absensi.</p>
                        @endforelse
                    </div>
                </x-ui.card>
            </div>
        </div>
    @endif
</x-app-layout>
