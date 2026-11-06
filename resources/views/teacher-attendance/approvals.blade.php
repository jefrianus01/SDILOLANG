<x-app-layout title="Persetujuan Izin">
    <div class="space-y-5">
        <x-ui.page-header
            eyebrow="Absensi"
            title="Persetujuan Izin"
            description="Pengajuan izin, sakit, dan dinas luar guru yang menunggu keputusan Kepala Sekolah.">
            <x-slot name="actions">
                <a href="{{ route('teacher-attendance.admin') }}" class="btn btn-secondary">Lihat Semua Absensi</a>
            </x-slot>
        </x-ui.page-header>

        <div class="space-y-4">
            @forelse ($attendances as $attendance)
                <x-ui.card>
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <p class="text-base font-semibold text-gray-900">{{ $attendance->teacher?->nama ?? '-' }}</p>
                                <x-ui.badge :status="$attendance->status->value" />
                                <x-ui.badge :status="$attendance->approval_status->value" />
                            </div>
                            <p class="mt-1 text-sm text-gray-500">
                                {{ $attendance->teacher?->teacherType?->name ?? 'Guru' }}
                                &middot; {{ $attendance->date->translatedFormat('l, d F Y') }}
                            </p>
                            <p class="mt-3 whitespace-pre-line rounded-lg bg-gray-50 p-3 text-sm text-gray-700">{{ $attendance->reason ?: 'Tanpa keterangan.' }}</p>

                            @if ($attendance->attachment_path)
                                <a href="{{ Storage::disk('public')->url($attendance->attachment_path) }}" target="_blank"
                                   class="mt-3 inline-block text-sm font-semibold text-brand-600 hover:text-brand-700">
                                    Lihat lampiran &rarr;
                                </a>
                            @endif
                        </div>

                        <div class="w-full space-y-3 sm:w-72">
                            <form method="POST" action="{{ route('teacher-attendance.approve', $attendance) }}" class="space-y-2">
                                @csrf
                                <x-input-label for="approve-note-{{ $attendance->id }}" value="Catatan (opsional)" />
                                <textarea id="approve-note-{{ $attendance->id }}" name="note" rows="2"
                                          class="field"></textarea>
                                <button class="btn btn-primary w-full">Setujui</button>
                            </form>

                            <form method="POST" action="{{ route('teacher-attendance.reject', $attendance) }}" class="space-y-2"
                                  onsubmit="return confirm('Tolak pengajuan ini? Status akan dicatat sebagai alpa.')">
                                @csrf
                                <x-input-label for="reject-note-{{ $attendance->id }}" value="Alasan penolakan" />
                                <textarea id="reject-note-{{ $attendance->id }}" name="note" rows="2" placeholder="Alasan penolakan"
                                          class="field"></textarea>
                                <button class="btn btn-danger w-full">Tolak</button>
                            </form>
                        </div>
                    </div>
                </x-ui.card>
            @empty
                <x-ui.card>
                    <p class="py-6 text-center text-sm text-gray-500">Tidak ada pengajuan yang menunggu persetujuan.</p>
                </x-ui.card>
            @endforelse
        </div>

        @if ($attendances->hasPages())
            <div>{{ $attendances->links() }}</div>
        @endif
    </div>
</x-app-layout>
