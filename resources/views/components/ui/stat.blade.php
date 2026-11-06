@props(['label', 'value', 'hint' => null, 'tone' => 'brand'])

@php
    $tones = [
        'brand' => 'bg-brand-600',
        'indigo' => 'bg-brand-600',
        'emerald' => 'bg-emerald-500',
        'amber' => 'bg-amber-500',
        'rose' => 'bg-rose-500',
        'sky' => 'bg-sky-500',
        'slate' => 'bg-gray-400',
    ];
@endphp

<div {{ $attributes->merge(['class' => 'rounded-xl border border-gray-200 bg-white p-5 shadow-card']) }}>
    <div class="flex items-start justify-between gap-3">
        <div class="flex min-w-0 items-center gap-2">
            <span class="h-1.5 w-1.5 shrink-0 rounded-full {{ $tones[$tone] ?? $tones['brand'] }}" aria-hidden="true"></span>
            <p class="truncate text-[13px] font-medium text-gray-500">{{ $label }}</p>
        </div>

        @isset($icon)
            <span class="shrink-0 text-gray-300">{{ $icon }}</span>
        @endisset
    </div>

    <p class="mt-3 text-[28px] font-semibold leading-none tracking-tight text-gray-900 tabular-nums">{{ $value }}</p>

    @if ($hint)
        <p class="mt-2 text-xs text-gray-400">{{ $hint }}</p>
    @endif
</div>
