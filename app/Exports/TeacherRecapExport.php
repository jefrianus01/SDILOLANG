<?php

namespace App\Exports;

use App\Enums\TeacherAttendanceStatus;
use Maatwebsite\Excel\Concerns\Export;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class TeacherRecapExport implements Export, FromArray, ShouldAutoSize, WithColumnFormatting, WithHeadings, WithTitle
{
    /**
     * @param  array<string, mixed>  $recap
     */
    public function __construct(private readonly array $recap)
    {
    }

    public function array(): array
    {
        $rows = [];

        foreach ($this->recap['rows'] as $row) {
            $line = [
                $row['no'],
                $row['teacher']->nama,
                (string) $row['teacher']->nip_nik,
                $row['teacher']->teacherType?->name ?? '-',
            ];

            foreach (TeacherAttendanceStatus::values() as $status) {
                $line[] = $row['counts'][$status];
            }

            $line[] = $row['pending'];
            $rows[] = $line;
        }

        return $rows;
    }

    public function headings(): array
    {
        $headings = ['No', 'Nama Guru', 'NIP/NIK', 'Guru Apa'];

        foreach (TeacherAttendanceStatus::cases() as $status) {
            $headings[] = $status->label();
        }

        $headings[] = 'Menunggu Persetujuan';

        return $headings;
    }

    public function columnFormats(): array
    {
        return ['C' => NumberFormat::FORMAT_TEXT];
    }

    public function title(): string
    {
        return 'Rekap Guru '.$this->recap['month']->format('m-Y');
    }
}
