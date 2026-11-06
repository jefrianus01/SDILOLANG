@php
    $statusColumns = App\Enums\TeacherAttendanceStatus::cases();
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Rekap Absensi Guru</title>
    <style>
        @page { margin: 24px 28px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color: #111827; }
        .header { border-bottom: 2px solid #4338ca; padding-bottom: 8px; margin-bottom: 14px; }
        .header h1 { font-size: 15px; margin: 0; color: #312e81; }
        .header p { margin: 2px 0 0; font-size: 9px; color: #4b5563; }
        .meta { margin: 0 0 10px; font-size: 9px; color: #4b5563; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #d1d5db; padding: 4px 5px; text-align: center; }
        th { background: #eef2ff; font-size: 8px; text-transform: uppercase; color: #3730a3; }
        td.name { text-align: left; }
        tbody tr:nth-child(even) td { background: #f9fafb; }
        tfoot td { background: #f3f4f6; font-weight: bold; }
        .footer { margin-top: 14px; font-size: 8px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ \App\Support\SchoolSetting::name() }}</h1>
        <p>{{ \App\Support\SchoolSetting::address() ?: 'Alamat sekolah belum diatur' }}</p>
    </div>

    <p class="meta">
        <strong>Rekap Absensi Guru</strong>
        &middot; {{ $recap['month']->translatedFormat('F Y') }}
        &middot; Dicetak {{ now()->translatedFormat('d F Y H:i') }}
    </p>

    <table>
        <thead>
            <tr>
                <th style="width: 24px;">No</th>
                <th class="name">Nama Guru</th>
                <th style="width: 90px;">Jenis Guru</th>
                @foreach ($statusColumns as $status)
                    <th style="width: 45px;">{{ $status->label() }}</th>
                @endforeach
                <th style="width: 45px;">Menunggu</th>
                <th style="width: 40px;">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($recap['rows'] as $row)
                <tr>
                    <td>{{ $row['no'] }}</td>
                    <td class="name">
                        {{ $row['teacher']->nama }}
                        <br><span style="color:#6b7280;">{{ $row['teacher']->nip_nik }}</span>
                    </td>
                    <td>{{ $row['teacher']->teacherType?->name ?? '-' }}</td>
                    @foreach ($statusColumns as $status)
                        <td>{{ $row['counts'][$status->value] ?? 0 }}</td>
                    @endforeach
                    <td>{{ $row['pending'] }}</td>
                    <td>{{ $row['recorded'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($statusColumns) + 5 }}">Belum ada guru aktif.</td>
                </tr>
            @endforelse
        </tbody>
        @if (! empty($recap['rows']))
            <tfoot>
                <tr>
                    <td colspan="3">Total</td>
                    @foreach ($statusColumns as $status)
                        <td>{{ $recap['totals'][$status->value] ?? 0 }}</td>
                    @endforeach
                    <td>{{ $recap['totals']['pending'] ?? 0 }}</td>
                    <td>{{ collect($recap['totals'])->except('pending')->sum() }}</td>
                </tr>
            </tfoot>
        @endif
    </table>

    <p class="footer">
        Pengajuan yang masih menunggu persetujuan dihitung pada kolom "Menunggu" dan tetap masuk pada status terkait.
    </p>
</body>
</html>
