<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Data Siswa</title>
    <style>
        @page { margin: 24px 28px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #111827; }
        .header { border-bottom: 2px solid #4338ca; padding-bottom: 8px; margin-bottom: 14px; }
        .header h1 { font-size: 15px; margin: 0; color: #312e81; }
        .header p { margin: 2px 0 0; font-size: 9px; color: #4b5563; }
        .meta { margin: 0 0 10px; font-size: 9px; color: #4b5563; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #d1d5db; padding: 5px 6px; text-align: left; vertical-align: top; }
        th { background: #eef2ff; font-size: 9px; text-transform: uppercase; letter-spacing: .03em; color: #3730a3; }
        tbody tr:nth-child(even) td { background: #f9fafb; }
        .center { text-align: center; }
        .footer { margin-top: 14px; font-size: 8px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ \App\Support\SchoolSetting::name() }}</h1>
        <p>{{ \App\Support\SchoolSetting::address() ?: 'Alamat sekolah belum diatur' }}</p>
    </div>

    <p class="meta">
        <strong>Data Siswa</strong> &middot; Dicetak {{ now()->translatedFormat('d F Y H:i') }} &middot; Total {{ $students->count() }} siswa
    </p>

    <table>
        <thead>
            <tr>
                <th class="center" style="width: 30px;">No</th>
                <th style="width: 130px;">Nomor Induk Siswa</th>
                <th>Nama</th>
                <th style="width: 110px;">Kelas</th>
                <th style="width: 65px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($students as $student)
                <tr>
                    <td class="center">{{ $loop->iteration }}</td>
                    <td>{{ $student->nomor_induk_siswa }}</td>
                    <td>{{ $student->nama }}</td>
                    <td>{{ $student->classroom?->name ?? '-' }}</td>
                    <td>{{ ucfirst($student->status->value) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="center">Belum ada data siswa.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <p class="footer">Dokumen ini dihasilkan otomatis oleh Sistem Absensi {{ \App\Support\SchoolSetting::name() }}.</p>
</body>
</html>
