@props(['name', 'class' => 'h-4 w-4'])

@php
    $paths = [
        'dashboard' => '<rect x="3" y="3" width="7.5" height="7.5" rx="1.6"/><rect x="13.5" y="3" width="7.5" height="7.5" rx="1.6"/><rect x="3" y="13.5" width="7.5" height="7.5" rx="1.6"/><rect x="13.5" y="13.5" width="7.5" height="7.5" rx="1.6"/>',
        'calendar' => '<rect x="3.5" y="5" width="17" height="16" rx="2"/><path d="M8 3v4M16 3v4M3.5 10.5h17"/>',
        'calendar-check' => '<rect x="3.5" y="5" width="17" height="16" rx="2"/><path d="M8 3v4M16 3v4M3.5 10.5h17"/><path d="M9 15.2l2.2 2.2 4-4.4"/>',
        'clipboard' => '<rect x="5" y="4.5" width="14" height="16.5" rx="2"/><path d="M9 4.5V3.5A1.5 1.5 0 0 1 10.5 2h3A1.5 1.5 0 0 1 15 3.5v1"/><path d="M9 10h6M9 14h6M9 18h3"/>',
        'user-check' => '<circle cx="10" cy="8" r="3.5"/><path d="M4 20c0-3.2 2.7-5.5 6-5.5 1.4 0 2.7.4 3.7 1.2"/><path d="M15.5 17.5l1.8 1.8 3.2-3.6"/>',
        'shield-check' => '<path d="M12 3l7.5 3v5.6c0 4.6-3.2 7.7-7.5 9.4-4.3-1.7-7.5-4.8-7.5-9.4V6L12 3z"/><path d="M9 11.8l2.2 2.2 4-4.4"/>',
        'users' => '<circle cx="9.5" cy="8" r="3.3"/><path d="M3.5 20c0-3.1 2.7-5.4 6-5.4s6 2.3 6 5.4"/><path d="M16 5.2a3.2 3.2 0 0 1 0 6.1"/><path d="M17.8 20c0-2.1-.7-3.8-2-4.7"/>',
        'tag' => '<path d="M4 4h7l9 9-7 7-9-9V4z"/><circle cx="8.4" cy="8.4" r="1.2"/>',
        'building' => '<path d="M4 21V10l8-6 8 6v11"/><path d="M3 21h18"/><path d="M9.5 21v-6h5v6"/><path d="M12 10.5h.01"/>',
        'graduation-cap' => '<path d="M3 9.5L12 5l9 4.5-9 4.5-9-4.5z"/><path d="M7 11.8V16c0 1.7 2.2 3 5 3s5-1.3 5-3v-4.2"/>',
        'chart' => '<path d="M4 20V5"/><path d="M4 20h16"/><path d="M8 20v-5.5M12.5 20v-9M17 20v-3.5"/>',
        'newspaper' => '<rect x="3.5" y="4.5" width="17" height="15" rx="2"/><path d="M7.5 9h9M7.5 13h9M7.5 17h5"/>',
        'photo' => '<rect x="3" y="4.5" width="18" height="15" rx="2"/><circle cx="8.5" cy="9.5" r="1.5"/><path d="M21 16l-5-5-6 6"/>',
        'settings' => '<path d="M4 7h9M17 7h3M4 17h3M11 17h9"/><circle cx="15" cy="7" r="2"/><circle cx="9" cy="17" r="2"/>',
        'user' => '<circle cx="12" cy="8" r="3.5"/><path d="M5 20c0-3.3 3.1-5.6 7-5.6s7 2.3 7 5.6"/>',
        'activity' => '<path d="M4 6h16M4 12h16M4 18h10"/>',
        'external' => '<path d="M14 4h6v6"/><path d="M20 4l-8 8"/><path d="M19 14v4a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h4"/>',
        'menu' => '<path d="M4 7h16M4 12h16M4 17h16"/>',
        'x' => '<path d="M6 6l12 12M18 6L6 18"/>',
        'chevron-down' => '<path d="M6 9.5l6 6 6-6"/>',
        'chevron-right' => '<path d="M9.5 6l6 6-6 6"/>',
        'search' => '<circle cx="11" cy="11" r="6.5"/><path d="M16 16l4.5 4.5"/>',
        'logout' => '<path d="M15 4h3a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-3"/><path d="M10 8l-4 4 4 4"/><path d="M6 12h9"/>',
        'clock' => '<circle cx="12" cy="12" r="8.5"/><path d="M12 7.5V12l3 2"/>',
        'map-pin' => '<path d="M12 21s7-5.5 7-11a7 7 0 1 0-14 0c0 5.5 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/>',
        'check' => '<path d="M5 12.5l4.5 4.5L19 7"/>',
        'alert' => '<path d="M12 4.5l8.5 15h-17L12 4.5z"/><path d="M12 10v4.5"/><path d="M12 17.5h.01"/>',
        'info' => '<circle cx="12" cy="12" r="8.5"/><path d="M12 11v5"/><path d="M12 8h.01"/>',
        'arrow-right' => '<path d="M5 12h14"/><path d="M13 6l6 6-6 6"/>',
        'plus' => '<path d="M12 5v14M5 12h14"/>',
        'download' => '<path d="M12 4v11"/><path d="M8 11l4 4 4-4"/><path d="M4 19h16"/>',
        'upload' => '<path d="M12 16V5"/><path d="M8 9l4-4 4 4"/><path d="M4 19h16"/>',
        'filter' => '<path d="M4 6h16l-6 7v5l-4 2v-7L4 6z"/>',
        'lock' => '<rect x="5" y="10.5" width="14" height="10" rx="2"/><path d="M8.5 10.5V8a3.5 3.5 0 0 1 7 0v2.5"/>',
        'mail' => '<rect x="3" y="5.5" width="18" height="13" rx="2"/><path d="M4 7l8 6 8-6"/>',
        'phone' => '<path d="M5 4h3l1.5 4-2 1.5a11 11 0 0 0 5 5L14 12.5 18 14v3a2 2 0 0 1-2 2A14 14 0 0 1 3 6a2 2 0 0 1 2-2z"/>',
        'eye' => '<path d="M3 12s3.5-6 9-6 9 6 9 6-3.5 6-9 6-9-6-9-6z"/><circle cx="12" cy="12" r="2.6"/>',
        'key' => '<circle cx="8" cy="8" r="4"/><path d="M11 11l8 8"/><path d="M16.5 16.5l2-2"/><path d="M19 19l2-2"/>',
        'dot' => '<circle cx="12" cy="12" r="3"/>',
    ];
@endphp

<svg {{ $attributes->merge([
    'class' => $class,
    'viewBox' => '0 0 24 24',
    'fill' => 'none',
    'stroke' => 'currentColor',
    'stroke-width' => '1.6',
    'stroke-linecap' => 'round',
    'stroke-linejoin' => 'round',
    'aria-hidden' => 'true',
]) }}>
    {!! $paths[$name] ?? $paths['dot'] !!}
</svg>
