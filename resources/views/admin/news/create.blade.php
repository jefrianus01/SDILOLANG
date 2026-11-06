<x-app-layout title="Tulis Berita">
    <div class="mx-auto max-w-5xl space-y-5">
        <a href="{{ route('admin.news.index') }}" class="text-sm font-semibold text-brand-600 hover:text-brand-700">&larr; Kembali ke daftar berita</a>

        <x-ui.card title="Berita Baru">
            <form method="POST" action="{{ route('admin.news.store') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf
                @include('admin.news.form', ['news' => null])

                <div class="flex items-center gap-3">
                    <button class="btn btn-primary">Simpan Berita</button>
                    <a href="{{ route('admin.news.index') }}" class="text-sm font-semibold text-gray-500 hover:text-gray-700">Batal</a>
                </div>
            </form>
        </x-ui.card>
    </div>
</x-app-layout>
