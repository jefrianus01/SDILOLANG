@php
    $gallery = $gallery ?? null;
@endphp

<div class="grid gap-4 lg:grid-cols-3">
    <div class="space-y-4 lg:col-span-2">
        <div>
            <x-input-label for="title" value="Judul foto" />
            <x-text-input id="title" name="title" :value="old('title', $gallery?->title)" class="mt-1 block w-full" required />
            <x-input-error :messages="$errors->get('title')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="caption" value="Keterangan" />
            <textarea id="caption" name="caption" rows="3" required
                      class="field mt-1">{{ old('caption', $gallery?->caption) }}</textarea>
            <x-input-error :messages="$errors->get('caption')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="sort_order" value="Urutan tampil" />
            <x-text-input id="sort_order" name="sort_order" type="number" min="0" max="9999" :value="old('sort_order', $gallery?->sort_order ?? 0)" class="mt-1 block w-full" />
            <p class="mt-1 text-xs text-gray-500">Angka kecil tampil lebih dahulu.</p>
            <x-input-error :messages="$errors->get('sort_order')" class="mt-2" />
        </div>

        <label class="flex items-start gap-3 rounded-lg border border-gray-200 p-3">
            <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $gallery?->is_published))
                   class="mt-0.5 rounded border-gray-300 text-brand-600 focus:ring-brand-500">
            <span class="text-sm">
                <span class="font-semibold text-gray-800">Publikasikan</span>
                <span class="block text-xs text-gray-500">Foto hanya tampil di website jika dicentang.</span>
            </span>
        </label>
    </div>

    <div>
        <x-input-label for="image" value="Foto" />
        @if ($gallery?->image_path)
            <img src="{{ Storage::disk('public')->url($gallery->image_path) }}" alt="" class="mt-2 h-40 w-full rounded-lg object-cover">
        @endif
        <input id="image" type="file" name="image" accept=".jpg,.jpeg,.png,.webp" @if (! $gallery) required @endif
               class="mt-2 block w-full field-file">
        <p class="mt-1 text-xs text-gray-500">JPG, PNG, atau WEBP maksimal 4 MB.</p>
        <x-input-error :messages="$errors->get('image')" class="mt-2" />
    </div>
</div>
