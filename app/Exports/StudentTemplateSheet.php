<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class StudentTemplateSheet implements FromArray, ShouldAutoSize, WithColumnFormatting, WithHeadings, WithTitle
{
    public function array(): array
    {
        return [];
    }

    public function headings(): array
    {
        return ['nomor_induk_siswa', 'nama'];
    }

    public function columnFormats(): array
    {
        return ['A' => NumberFormat::FORMAT_TEXT];
    }

    public function title(): string
    {
        return 'Data Siswa';
    }
}
