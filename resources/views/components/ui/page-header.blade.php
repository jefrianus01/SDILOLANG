@props(['title' => null, 'description' => null, 'eyebrow' => null])

<div {{ $attributes->merge(['class' => 'flex flex-wrap items-start justify-between gap-4']) }}>
    <div class="min-w-0">
        @if ($eyebrow)
            <p class="text-2xs font-semibold uppercase tracking-[0.18em] text-gold-dark">{{ $eyebrow }}</p>
        @endif

        @if ($title)
            <h1 class="mt-1 text-[22px] font-semibold leading-tight text-gray-900">{{ $title }}</h1>
        @endif

        @if ($description)
            <p class="mt-1.5 max-w-2xl text-sm leading-6 text-gray-500">{{ $description }}</p>
        @endif

        {{ $slot }}
    </div>

    @isset($actions)
        <div class="flex flex-wrap items-center gap-2">{{ $actions }}</div>
    @endisset
</div>
