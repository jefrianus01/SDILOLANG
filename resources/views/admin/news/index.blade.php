<x-app-layout title="Kelola Berita">
    <div class="space-y-5">
        <x-ui.page-header
            eyebrow="Website"
            title="Kelola Berita"
            description="Berita yang dipublikasikan akan tampil di website sekolah.">
            <x-slot name="actions">
                @can('create', App\Models\News::class)
                    <a href="{{ route('admin.news.create') }}" class="btn btn-primary">+ Tulis Berita</a>
                @endcan
            </x-slot>
        </x-ui.page-header>

        <x-ui.card padding="p-0">
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead class="bg-gray-50/80">
                        <tr>
                            <th class="w-12 px-4 py-3">No</th>
                            <th class="px-4 py-3">Judul</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Tanggal Publikasi</th>
                            <th class="px-4 py-3">Penulis</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($news as $item)
                            <tr>
                                <td class="px-4 py-3 text-gray-500">{{ $news->firstItem() + $loop->index }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        @if ($item->image_path)
                                            <img src="{{ Storage::disk('public')->url($item->image_path) }}" alt="" class="h-10 w-14 rounded object-cover">
                                        @endif
                                        <div class="min-w-0">
                                            <p class="font-semibold text-gray-900">{{ $item->title }}</p>
                                            <p class="truncate text-xs text-gray-500">/berita/{{ $item->slug }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3"><x-ui.badge :status="$item->is_published ? 'aktif' : 'nonaktif'" /></td>
                                <td class="px-4 py-3 text-gray-600">{{ $item->published_at?->translatedFormat('d M Y H:i') ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $item->author?->name ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap items-center justify-end gap-2">
                                        @can('update', $item)
                                            <a href="{{ route('admin.news.edit', $item) }}" class="btn btn-secondary btn-xs">Edit</a>
                                        @endcan
                                        @can('delete', $item)
                                            <form method="POST" action="{{ route('admin.news.destroy', $item) }}"
                                                  data-confirm="Hapus berita ini?"
                                                  data-confirm-title="Hapus berita"
                                                  data-confirm-label="Ya, hapus"
                                                  data-confirm-tone="danger">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-danger btn-xs">Hapus</button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <x-ui.empty-row :colspan="6" message="Belum ada berita." />
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($news->hasPages())
                <div class="border-t border-gray-100 px-4 py-3">{{ $news->links() }}</div>
            @endif
        </x-ui.card>
    </div>
</x-app-layout>
