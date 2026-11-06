@php
    $news = $news ?? null;
@endphp

<div class="grid gap-4 lg:grid-cols-3">
    <div class="space-y-4 lg:col-span-2">
        <div>
            <x-input-label for="title" value="Judul berita" />
            <x-text-input id="title" name="title" :value="old('title', $news?->title)" class="mt-1 block w-full" required />
            <x-input-error :messages="$errors->get('title')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="excerpt" value="Ringkasan (opsional)" />
            <textarea id="excerpt" name="excerpt" rows="2"
                      class="field mt-1">{{ old('excerpt', $news?->excerpt) }}</textarea>
            <x-input-error :messages="$errors->get('excerpt')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="body" value="Isi berita" />
            <textarea id="body" name="body" rows="14" required
                      class="field mt-1">{{ old('body', $news?->body) }}</textarea>
            <x-input-error :messages="$errors->get('body')" class="mt-2" />
        </div>
    </div>

    <div class="space-y-4">
        <div>
            <x-input-label for="image" value="Gambar utama" />
            @if ($news?->image_path)
                <img src="{{ Storage::disk('public')->url($news->image_path) }}" alt="" class="mt-2 h-32 w-full rounded-lg object-cover">
            @endif
            <input id="image" type="file" name="image" accept=".jpg,.jpeg,.png,.webp"
                   class="mt-2 block w-full field-file">
            <p class="mt-1 text-xs text-gray-500">Maksimal 4 MB.</p>
            <x-input-error :messages="$errors->get('image')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="published_at" value="Tanggal publikasi (opsional)" />
            <input id="published_at" type="datetime-local" name="published_at"
                   value="{{ old('published_at', $news?->published_at?->format('Y-m-d\TH:i')) }}"
                   class="field mt-1">
            <p class="mt-1 text-xs text-gray-500">Kosongkan untuk otomatis memakai waktu sekarang saat dipublikasikan.</p>
            <x-input-error :messages="$errors->get('published_at')" class="mt-2" />
        </div>

        <label class="flex items-start gap-3 rounded-lg border border-gray-200 p-3">
            <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $news?->is_published))
                   class="mt-0.5 rounded border-gray-300 text-brand-600 focus:ring-brand-500">
            <span class="text-sm">
                <span class="font-semibold text-gray-800">Publikasikan</span>
                <span class="block text-xs text-gray-500">Jika tidak dicentang, berita disimpan sebagai draf.</span>
            </span>
        </label>
    </div>
</div>
