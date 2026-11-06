<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Export;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class TeacherTemplateExport implements Export, WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new TeacherTemplateSheet,
            new InstructionsSheet('Petunjuk', [
                'Isi sheet "Data Guru" mulai baris 2. Jangan mengubah nama kolom.',
                'nama: nama lengkap guru (wajib, maksimal 150 karakter).',
                'nip_nik: NIP atau NIK, hanya angka 16-18 digit, harus unik (wajib).',
                'jenis_guru: harus sama dengan nama pada master Jenis Guru (wajib).',
                'status: aktif atau nonaktif (opsional, default aktif).',
                'Password awal dibuat acak oleh sistem dan hanya ditampilkan sekali setelah import.',
                'Baris yang tidak valid akan dilewati dan dilaporkan beserta nomor barisnya.',
                'Format kolom NIP/NIK sudah diset sebagai Teks agar angka tidak berubah.',
            ]),
        ];
    }
}
