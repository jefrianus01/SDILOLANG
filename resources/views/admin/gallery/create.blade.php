<x-app-layout title="Tambah Foto Galeri">
    <div class="mx-auto max-w-5xl space-y-5">
        <a href="{{ route('admin.gallery.index') }}" class="text-sm font-semibold text-brand-600 hover:text-brand-700">&larr; Kembali ke galeri</a>

        <x-ui.card title="Foto Baru">
            <form method="POST" action="{{ route('admin.gallery.store') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf
                @include('admin.gallery.form', ['gallery' => null])

                <div class="flex items-center gap-3">
                    <button class="btn btn-primary">Simpan Foto</button>
                    <a href="{{ route('admin.gallery.index') }}" class="text-sm font-semibold text-gray-500 hover:text-gray-700">Batal</a>
                </div>
            </form>
        </x-ui.card>
    </div>
</x-app-layout>
