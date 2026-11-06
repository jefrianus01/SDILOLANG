@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi halaman" class="flex flex-wrap items-center justify-between gap-3">
        <p class="text-[13px] text-gray-500">
            Menampilkan
            <span class="font-semibold text-gray-700">{{ $paginator->firstItem() }}</span>
            –
            <span class="font-semibold text-gray-700">{{ $paginator->lastItem() }}</span>
            dari
            <span class="font-semibold text-gray-700">{{ $paginator->total() }}</span>
        </p>

        <div class="flex items-center gap-1">
            @if ($paginator->onFirstPage())
                <span class="btn btn-secondary btn-sm cursor-not-allowed opacity-50" aria-disabled="true">
                    <x-ui.icon name="chevron-right" class="h-3.5 w-3.5 rotate-180" />
                    Sebelumnya
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="btn btn-secondary btn-sm">
                    <x-ui.icon name="chevron-right" class="h-3.5 w-3.5 rotate-180" />
                    Sebelumnya
                </a>
            @endif

            <div class="hidden items-center gap-1 sm:flex">
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span class="px-1.5 text-[13px] text-gray-400">{{ $element }}</span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page" class="btn btn-primary btn-sm min-w-[34px] tabular-nums">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="btn btn-ghost btn-sm min-w-[34px] tabular-nums">{{ $page }}</a>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </div>

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="btn btn-secondary btn-sm">
                    Berikutnya
                    <x-ui.icon name="chevron-right" class="h-3.5 w-3.5" />
                </a>
            @else
                <span class="btn btn-secondary btn-sm cursor-not-allowed opacity-50" aria-disabled="true">
                    Berikutnya
                    <x-ui.icon name="chevron-right" class="h-3.5 w-3.5" />
                </span>
            @endif
        </div>
    </nav>
@endif
