<x-app-layout title="Edit Berita">
    <div class="mx-auto max-w-5xl space-y-5">
        <a href="{{ route('admin.news.index') }}" class="text-sm font-semibold text-brand-600 hover:text-brand-700">&larr; Kembali ke daftar berita</a>

        <x-ui.card title="Edit Berita" :description="$news->title">
            <form method="POST" action="{{ route('admin.news.update', $news) }}" enctype="multipart/form-data" class="space-y-5">
                @csrf
                @method('PUT')
                @include('admin.news.form', ['news' => $news])

                <div class="flex items-center gap-3">
                    <button class="btn btn-primary">Perbarui Berita</button>
                    <a href="{{ route('admin.news.index') }}" class="text-sm font-semibold text-gray-500 hover:text-gray-700">Batal</a>
                </div>
            </form>
        </x-ui.card>
    </div>
</x-app-layout>
