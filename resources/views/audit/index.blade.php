<x-app-layout title="Log Aktivitas">
    <div class="space-y-5">
        <x-ui.page-header
            eyebrow="Sistem"
            title="Log Aktivitas"
            description="Catatan aktivitas penting: perubahan data guru, siswa, absensi, pengaturan, dan konten website.">
            <x-slot name="actions">
                <form method="GET" action="{{ route('audit.index') }}" class="flex items-end gap-2">
                    <div>
                        <x-input-label for="search" value="Cari aksi / keterangan" />
                        <x-text-input id="search" name="search" :value="$filters['search'] ?? ''" class="mt-1" placeholder="Contoh: teacher.created" />
                    </div>
                    <button class="btn btn-primary">Cari</button>
                    @if (! empty($filters['search']))
                        <a href="{{ route('audit.index') }}" class="text-sm font-semibold text-gray-500 hover:text-gray-700">Reset</a>
                    @endif
                </form>
            </x-slot>
        </x-ui.page-header>

        <x-ui.card padding="p-0">
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead class="bg-gray-50/80">
                        <tr>
                            <th class="px-4 py-3">Waktu</th>
                            <th class="px-4 py-3">Pengguna</th>
                            <th class="px-4 py-3">Aksi</th>
                            <th class="px-4 py-3">Keterangan</th>
                            <th class="px-4 py-3">IP</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($logs as $log)
                            <tr>
                                <td class="whitespace-nowrap px-4 py-3 text-gray-600">{{ $log->created_at?->translatedFormat('d M Y H:i') }}</td>
                                <td class="px-4 py-3">
                                    <p class="font-semibold text-gray-900">{{ $log->user?->name ?? 'Sistem' }}</p>
                                    <p class="text-xs text-gray-500">{{ $log->user?->username ?? '-' }}</p>
                                </td>
                                <td class="px-4 py-3"><code class="rounded bg-gray-100 px-1.5 py-0.5 text-xs text-gray-700">{{ $log->action }}</code></td>
                                <td class="px-4 py-3 text-gray-700">{{ $log->description ?? '-' }}</td>
                                <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $log->ip_address ?? '-' }}</td>
                            </tr>
                        @empty
                            <x-ui.empty-row :colspan="5" message="Belum ada log aktivitas." />
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($logs->hasPages())
                <div class="border-t border-gray-100 px-4 py-3">{{ $logs->links() }}</div>
            @endif
        </x-ui.card>
    </div>
</x-app-layout>
