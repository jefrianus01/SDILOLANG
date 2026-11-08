<x-public-layout title="Berita">
    <section class="border-b border-gray-200 bg-gradient-to-b from-brand-50/80 to-white">
        <div class="mx-auto max-w-6xl px-4 py-12 sm:px-6">
            <div class="flex flex-wrap items-end justify-between gap-6">
                <div>
                    <p class="text-2xs font-semibold uppercase tracking-[0.18em] text-gold-dark">Kabar sekolah</p>
                    <h1 class="mt-3 font-display text-[40px] font-semibold leading-tight text-ink">Berita terbaru</h1>
                    <p class="mt-3 max-w-xl text-sm leading-7 text-gray-600">
                        Informasi kegiatan, prestasi, dan pengumuman resmi {{ \App\Support\SchoolSetting::name() }}.
                    </p>
                </div>

                <form method="GET" action="{{ route('news.index') }}" class="flex w-full max-w-sm items-center gap-2">
                    <div class="relative flex-1">
                        <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-gray-400">
                            <x-ui.icon name="search" class="h-4 w-4" />
                        </span>
                        <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari berita..."
                               class="field pl-9">
                    </div>
                    <button class="btn btn-primary shrink-0">Cari</button>
                </form>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-4 py-12 sm:px-6">
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @forelse ($news as $item)
                <article class="group overflow-hidden rounded-xl border border-gray-200 bg-white transition-colors hover:border-gray-300">
                    <a href="{{ route('news.show', $item->slug) }}" class="block" tabindex="-1" aria-hidden="true">
                        @if ($item->image_path)
                            <img src="{{ Storage::disk('public')->url($item->image_path) }}" alt="{{ $item->title }}"
                                 class="h-44 w-full object-cover">
                        @else
                            <div class="flex h-44 items-center justify-center border-b border-gray-100 bg-parchment">
                                <span class="text-2xs font-semibold uppercase tracking-[0.2em] text-gold-dark">Berita Sekolah</span>
                            </div>
                        @endif
                    </a>

                    <div class="p-5">
                        <p class="text-2xs font-semibold uppercase tracking-[0.12em] text-gray-400">
                            {{ $item->published_at?->translatedFormat('d F Y') ?? $item->created_at?->translatedFormat('d F Y') }}
                        </p>
                        <h2 class="mt-2 font-display text-[22px] font-semibold leading-snug text-ink">
                            <a href="{{ route('news.show', $item->slug) }}" class="transition-colors hover:text-wine">{{ $item->title }}</a>
                        </h2>
                        <p class="mt-2 line-clamp-3 text-[13px] leading-6 text-gray-600">{{ $item->excerpt }}</p>
                    </div>
                </article>
            @empty
                <div class="rounded-xl border border-dashed border-gray-300 bg-white p-10 text-center md:col-span-2 lg:col-span-3">
                    <p class="text-sm font-medium text-gray-700">Tidak ada berita yang cocok.</p>
                    <p class="mt-1 text-[13px] text-gray-500">Coba kata kunci lain atau kembali ke semua berita.</p>
                    <a href="{{ route('news.index') }}" class="btn btn-secondary btn-sm mt-4">Tampilkan semua</a>
                </div>
            @endforelse
        </div>

        @if ($news->hasPages())
            <div class="mt-10">{{ $news->links() }}</div>
        @endif
    </section>
</x-public-layout>
