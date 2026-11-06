@props(['status' => null])

@php
    $map = [
        'aktif' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
        'nonaktif' => 'bg-gray-100 text-gray-600 ring-gray-500/20',
        'pindah' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
        'lulus' => 'bg-sky-50 text-sky-700 ring-sky-600/20',
        'hadir' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
        'terlambat' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
        'izin' => 'bg-sky-50 text-sky-700 ring-sky-600/20',
        'sakit' => 'bg-violet-50 text-violet-700 ring-violet-600/20',
        'dinas_luar' => 'bg-brand-50 text-brand-700 ring-brand-600/20',
        'alpa' => 'bg-rose-50 text-rose-700 ring-rose-600/20',
        'pending' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
        'approved' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
        'rejected' => 'bg-rose-50 text-rose-700 ring-rose-600/20',
        'not_required' => 'bg-gray-100 text-gray-600 ring-gray-500/20',
    ];

    $labels = [
        'dinas_luar' => 'Dinas Luar',
        'not_required' => '-',
        'pending' => 'Menunggu',
        'approved' => 'Disetujui',
        'rejected' => 'Ditolak',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 whitespace-nowrap rounded-full px-2 py-0.5 text-[11px] font-semibold ring-1 ring-inset '.($map[$status] ?? 'bg-gray-100 text-gray-700 ring-gray-500/20')]) }}>
    <span class="h-1.5 w-1.5 rounded-full bg-current opacity-70" aria-hidden="true"></span>
    {{ $labels[$status] ?? ucfirst((string) $status) }}
</span>
