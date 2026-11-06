@props(['title' => null, 'description' => null, 'padding' => 'p-5'])

<div {{ $attributes->merge(['class' => 'overflow-hidden rounded-xl border border-gray-200 bg-white shadow-card']) }}>
    @if ($title || $description || isset($actions) || isset($header))
        <div class="flex flex-wrap items-start justify-between gap-3 border-b border-gray-100 px-5 py-4">
            <div class="min-w-0">
                @if ($title)
                    <h2 class="text-[15px] font-semibold text-gray-900">{{ $title }}</h2>
                @endif
                @if ($description)
                    <p class="mt-0.5 text-[13px] leading-5 text-gray-500">{{ $description }}</p>
                @endif
                {{ $header ?? '' }}
            </div>

            @isset($actions)
                <div class="flex shrink-0 flex-wrap items-center gap-2">{{ $actions }}</div>
            @endisset
        </div>
    @endif

    <div class="{{ $padding }}">
        {{ $slot }}
    </div>
</div>
