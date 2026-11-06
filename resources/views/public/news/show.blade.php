<x-public-layout :title="$news->title">
    <article class="mx-auto max-w-3xl px-4 py-12 sm:px-6">
        <a href="{{ route('news.index') }}" class="link-arrow text-gray-500 transition-colors hover:text-ink">
            <x-ui.icon name="arrow-right" class="h-3.5 w-3.5 rotate-180" />
            Semua berita
        </a>

        <p class="mt-6 text-2xs font-semibold uppercase tracking-[0.16em] text-gold-dark">
            {{ $news->published_at?->translatedFormat('d F Y') ?? $news->created_at?->translatedFormat('d F Y') }}
        </p>

        <h1 class="mt-3 text-balance font-display text-[38px] font-semibold leading-[1.12] text-ink sm:text-[46px]">
            {{ $news->title }}
        </h1>

        @if ($news->author)
            <p class="mt-4 flex items-center gap-2 text-[13px] text-gray-500">
                <x-ui.icon name="user" class="h-3.5 w-3.5 text-gray-400" />
                {{ $news->author->name }}
            </p>
        @endif

        @if ($news->image_path)
            <img src="{{ Storage::disk('public')->url($news->image_path) }}" alt="{{ $news->title }}"
                 class="mt-8 w-full rounded-xl object-cover ring-1 ring-gray-200">
        @endif

        @if ($news->excerpt)
            <p class="mt-8 border-l-2 border-gold pl-5 font-display text-[21px] italic leading-relaxed text-ink/75">
                {{ $news->excerpt }}
            </p>
        @endif

        <div class="article-body mt-8">
            {!! $news->body !!}
        </div>

        @if ($related->isNotEmpty())
            <div class="mt-14 border-t border-gray-200 pt-8">
                <h2 class="font-display text-[26px] font-semibold text-ink">Berita lainnya</h2>

                <div class="mt-5 divide-y divide-gray-100 overflow-hidden rounded-xl border border-gray-200 bg-white">
                    @foreach ($related as $item)
                        <a href="{{ route('news.show', $item->slug) }}"
                           class="group flex items-start justify-between gap-4 px-5 py-4 transition-colors hover:bg-gray-50">
                            <div class="min-w-0">
                                <p class="text-2xs font-semibold uppercase tracking-[0.12em] text-gray-400">
                                    {{ $item->published_at?->translatedFormat('d F Y') }}
                                </p>
                                <p class="mt-1 text-[14px] font-semibold text-ink group-hover:text-wine">{{ $item->title }}</p>
                            </div>
                            <x-ui.icon name="chevron-right" class="mt-1 h-4 w-4 shrink-0 text-gray-300 group-hover:text-gray-500" />
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </article>
</x-public-layout>
