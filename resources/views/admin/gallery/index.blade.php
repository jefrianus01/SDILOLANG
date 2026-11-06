<x-app-layout title="Kelola Galeri">
    <div class="space-y-5">
        <x-ui.page-header
            eyebrow="Website"
            title="Kelola Galeri"
            description="Foto yang dipublikasikan akan tampil di halaman galeri sekolah.">
            <x-slot name="actions">
                @can('create', App\Models\Gallery::class)
                    <a href="{{ route('admin.gallery.create') }}" class="btn btn-primary">+ Tambah Foto</a>
                @endcan
            </x-slot>
        </x-ui.page-header>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @forelse ($galleries as $gallery)
                <x-ui.card padding="p-0">
                    <img src="{{ Storage::disk('public')->url($gallery->image_path) }}" alt="{{ $gallery->title }}" class="h-48 w-full rounded-t-xl object-cover">
                    <div class="space-y-2 p-4">
                        <div class="flex items-start justify-between gap-2">
                            <p class="font-semibold text-gray-900">{{ $gallery->title }}</p>
                            <x-ui.badge :status="$gallery->is_published ? 'aktif' : 'nonaktif'" />
                        </div>
                        <p class="text-sm text-gray-600">{{ $gallery->caption }}</p>
                        <p class="text-xs text-gray-400">Urutan: {{ $gallery->sort_order }}</p>
                        <div class="flex flex-wrap gap-2 pt-1">
                            @can('update', $gallery)
                                <a href="{{ route('admin.gallery.edit', $gallery) }}" class="btn btn-secondary btn-xs">Edit</a>
                            @endcan
                            @can('delete', $gallery)
                                <form method="POST" action="{{ route('admin.gallery.destroy', $gallery) }}"
                                      onsubmit="return confirm('Hapus foto ini dari galeri?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-xs">Hapus</button>
                                </form>
                            @endcan
                        </div>
                    </div>
                </x-ui.card>
            @empty
                <x-ui.card class="sm:col-span-2 xl:col-span-3">
                    <p class="py-6 text-center text-sm text-gray-500">Belum ada foto galeri.</p>
                </x-ui.card>
            @endforelse
        </div>

        @if ($galleries->hasPages())
            <div>{{ $galleries->links() }}</div>
        @endif
    </div>
</x-app-layout>
