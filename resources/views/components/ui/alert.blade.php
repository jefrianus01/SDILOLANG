@props(['type' => 'success', 'message' => null])

@php
    $styles = [
        'success' => 'border-emerald-200 bg-emerald-50 text-emerald-900',
        'error' => 'border-rose-200 bg-rose-50 text-rose-900',
        'warning' => 'border-amber-200 bg-amber-50 text-amber-900',
        'info' => 'border-sky-200 bg-sky-50 text-sky-900',
    ];

    $icons = [
        'success' => 'check',
        'error' => 'alert',
        'warning' => 'alert',
        'info' => 'info',
    ];

    $iconColors = [
        'success' => 'text-emerald-600',
        'error' => 'text-rose-600',
        'warning' => 'text-amber-600',
        'info' => 'text-sky-600',
    ];
@endphp

<div role="{{ $type === 'error' ? 'alert' : 'status' }}" {{ $attributes->merge(['class' => 'flex items-start gap-3 rounded-lg border px-4 py-3 text-sm '.($styles[$type] ?? $styles['info'])]) }}>
    <x-ui.icon :name="$icons[$type] ?? 'info'" class="mt-0.5 h-4 w-4 shrink-0 {{ $iconColors[$type] ?? $iconColors['info'] }}" />

    <div class="min-w-0 flex-1">
        {{ $message ?? $slot }}
    </div>
</div>
