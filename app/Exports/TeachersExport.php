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

class TeachersExport implements Export, FromArray, ShouldAutoSize, WithColumnFormatting, WithHeadings, WithTitle
{
    /**
     * @param  Collection<int, \App\Models\Teacher>  $teachers
     */
    public function __construct(private readonly Collection $teachers)
    {
    }

    public function array(): array
    {
        $rows = [];

        foreach ($this->teachers->values() as $index => $teacher) {
            $rows[] = [
                $index + 1,
                $teacher->nama,
                (string) $teacher->nip_nik,
                $teacher->teacherType?->name ?? '-',
                $teacher->status->label(),
            ];
        }

        return $rows;
    }

    public function headings(): array
    {
        return ['No', 'Nama Guru', 'NIP/NIK', 'Guru Apa', 'Status'];
    }

    public function columnFormats(): array
    {
        return ['C' => NumberFormat::FORMAT_TEXT];
    }

    public function title(): string
    {
        return 'Data Guru';
    }
}
