@php
    $classroom = $recap['classroom'];
    $statusColumns = App\Enums\AttendanceStatus::cases();
    $dayCount = count($recap['days']);
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Rekap Absensi {{ $classroom->name }}</title>
    <style>
        @page { margin: 18px 20px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 7px; color: #111827; }
        .header { border-bottom: 2px solid #4338ca; padding-bottom: 6px; margin-bottom: 10px; }
        .header h1 { font-size: 13px; margin: 0; color: #312e81; }
        .header p { margin: 2px 0 0; font-size: 8px; color: #4b5563; }
        .meta { margin: 0 0 8px; font-size: 8px; color: #374151; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #d1d5db; padding: 2px 3px; text-align: center; }
        th { background: #eef2ff; color: #3730a3; font-size: 6.5px; }
        td.name { text-align: left; }
        tfoot td { background: #f3f4f6; font-weight: bold; }
        .hadir { background: #d1fae5; }
        .sakit { background: #fef3c7; }
        .izin { background: #e0f2fe; }
        .alpa { background: #ffe4e6; }
        .footer { margin-top: 10px; font-size: 7px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ \App\Support\SchoolSetting::name() }}</h1>
        <p>{{ \App\Support\SchoolSetting::address() ?: 'Alamat sekolah belum diatur' }}</p>
    </div>

    <p class="meta">
        <strong>Rekap Absensi Siswa — {{ $classroom->name }}</strong>
        &middot; {{ $recap['month']->translatedFormat('F Y') }}
        &middot; Wali kelas: {{ $classroom->waliKelas?->nama ?? 'Belum ditunjuk' }}
        &middot; Dicetak {{ now()->translatedFormat('d F Y H:i') }}
    </p>

    <table>
        <thead>
            <tr>
                <th style="width: 18px;">No</th>
                <th class="name" style="width: 120px;">Nama</th>
                @foreach ($recap['days'] as $day)
                    <th>{{ $day }}</th>
                @endforeach
                @foreach ($statusColumns as $status)
                    <th>{{ substr($status->label(), 0, 1) }}</th>
                @endforeach
                <th>%</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($recap['rows'] as $row)
                <tr>
                    <td>{{ $row['no'] }}</td>
                    <td class="name">
                        {{ $row['student']->nama }}
                        <br><span style="color:#6b7280;">{{ $row['student']->nomor_induk_siswa }}</span>
                    </td>
                    @foreach ($recap['days'] as $day)
                        @php($status = $row['daily'][$day] ?? null)
                        <td class="{{ $status }}">{{ $status ? strtoupper(substr($status, 0, 1)) : '' }}</td>
                    @endforeach
                    @foreach ($statusColumns as $status)
                        <td>{{ $row['counts'][$status->value] ?? 0 }}</td>
                    @endforeach
                    <td>{{ number_format($row['percentage'], 1, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $dayCount + 6 }}">Belum ada siswa aktif di kelas ini.</td>
                </tr>
            @endforelse
        </tbody>
        @if (! empty($recap['rows']))
            <tfoot>
                <tr>
                    <td colspan="2">Total</td>
                    @foreach ($recap['days'] as $day)
                        <td></td>
                    @endforeach
                    @foreach ($statusColumns as $status)
                        <td>{{ $recap['totals'][$status->value] ?? 0 }}</td>
                    @endforeach
                    <td>{{ $recap['recorded'] }}</td>
                </tr>
            </tfoot>
        @endif
    </table>

    <p class="footer">
        Keterangan: H = Hadir, S = Sakit, I = Izin, A = Alpa. Persentase dihitung dari jumlah hari yang tercatat.
    </p>
</body>
</html>
