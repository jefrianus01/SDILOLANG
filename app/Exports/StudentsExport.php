<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Export;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class StudentsExport implements Export, FromArray, ShouldAutoSize, WithColumnFormatting, WithHeadings, WithTitle
{
    /**
     * @param  Collection<int, \App\Models\Student>  $students
     */
    public function __construct(private readonly Collection $students)
    {
    }

    public function array(): array
    {
        $rows = [];

        foreach ($this->students->values() as $index => $student) {
            $rows[] = [
                $index + 1,
                (string) $student->nomor_induk_siswa,
                $student->nama,
                $student->classroom?->name ?? '-',
            ];
        }

        return $rows;
    }

    public function headings(): array
    {
        return ['No', 'Nomor Induk Siswa', 'Nama', 'Kelas'];
    }

    public function columnFormats(): array
    {
        return ['B' => NumberFormat::FORMAT_TEXT];
    }

    public function title(): string
    {
        return 'Data Siswa';
    }
}
