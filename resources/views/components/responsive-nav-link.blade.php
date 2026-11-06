@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full border-l-2 border-gold bg-brand-50 py-2 pe-4 ps-3 text-start text-[13.5px] font-semibold text-brand-900 transition duration-150 ease-in-out focus:outline-none'
            : 'block w-full border-l-2 border-transparent py-2 pe-4 ps-3 text-start text-[13.5px] font-medium text-gray-600 transition duration-150 ease-in-out hover:border-gray-300 hover:bg-gray-50 hover:text-gray-900 focus:outline-none';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
