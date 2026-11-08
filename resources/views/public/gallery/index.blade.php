<x-public-layout title="Galeri">
    <section class="border-b border-gray-200 bg-gradient-to-b from-brand-50/80 to-white">
        <div class="mx-auto max-w-6xl px-4 py-12 sm:px-6">
            <p class="text-2xs font-semibold uppercase tracking-[0.18em] text-gold-dark">Dokumentasi</p>
            <h1 class="mt-3 font-display text-[40px] font-semibold leading-tight text-ink">Galeri kegiatan</h1>
            <p class="mt-3 max-w-xl text-sm leading-7 text-gray-600">
                Potret kegiatan belajar, acara sekolah, dan momen kebersamaan keluarga besar {{ \App\Support\SchoolSetting::name() }}.
            </p>
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-4 py-12 sm:px-6">
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($galleries as $gallery)
                <figure class="overflow-hidden rounded-xl border border-gray-200 bg-white">
                    <img src="{{ Storage::disk('public')->url($gallery->image_path) }}" alt="{{ $gallery->title }}"
                         class="h-56 w-full object-cover transition-transform duration-500 hover:scale-[1.02]">
                    <figcaption class="border-t border-gray-100 p-5">
                        <p class="text-[14px] font-semibold text-ink">{{ $gallery->title }}</p>
                        @if ($gallery->caption)
                            <p class="mt-1 line-clamp-2 text-[13px] leading-6 text-gray-500">{{ $gallery->caption }}</p>
                        @endif
                    </figcaption>
                </figure>
            @empty
                <div class="rounded-xl border border-dashed border-gray-300 bg-white p-10 text-center sm:col-span-2 lg:col-span-3">
                    <p class="text-sm font-medium text-gray-700">Belum ada foto galeri.</p>
                    <p class="mt-1 text-[13px] text-gray-500">Foto yang dipublikasikan akan tampil di halaman ini.</p>
                </div>
            @endforelse
        </div>

        @if ($galleries->hasPages())
            <div class="mt-10">{{ $galleries->links() }}</div>
        @endif
    </section>
</x-public-layout>
