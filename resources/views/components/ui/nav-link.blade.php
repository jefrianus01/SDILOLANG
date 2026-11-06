@props(['href', 'active' => false])

<a href="{{ $href }}"
   @if ($active) aria-current="page" @endif
   {{ $attributes->merge([
       'class' => 'group relative flex items-center gap-2.5 rounded-lg px-3 py-2 text-[13.5px] font-medium transition-colors '
           .($active ? 'bg-brand-50 text-brand-900' : 'text-gray-600 hover:bg-gray-100/80 hover:text-gray-900'),
   ]) }}>
    @if ($active)
        <span class="absolute inset-y-1.5 left-0 w-0.5 rounded-full bg-gold" aria-hidden="true"></span>
    @endif

    @isset($icon)
        <span class="shrink-0 {{ $active ? 'text-brand-700' : 'text-gray-400 group-hover:text-gray-500' }}">
            {{ $icon }}
        </span>
    @endisset

    <span class="truncate">{{ $slot }}</span>
</a>
