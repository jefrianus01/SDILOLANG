<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Export;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class StudentTemplateExport implements Export, WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new StudentTemplateSheet,
            new InstructionsSheet('Petunjuk', [
                'Isi sheet "Data Siswa" mulai baris 2. Jangan mengubah nama kolom.',
                'nomor_induk_siswa: nomor induk siswa, wajib, unik, maksimal 30 karakter.',
                'nama: nama lengkap siswa (wajib, maksimal 150 karakter).',
                'Kelas terisi otomatis sesuai kelas Anda sebagai wali kelas.',
                'Baris yang tidak valid akan dilewati dan dilaporkan beserta nomor barisnya.',
            ]),
        ];
    }
}
