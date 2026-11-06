<?php

namespace App\Exports;

use App\Enums\AttendanceStatus;
use Maatwebsite\Excel\Concerns\Export;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class ClassRecapExport implements Export, FromArray, ShouldAutoSize, WithHeadings, WithTitle
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
                (string) $row['student']->nomor_induk_siswa,
                $row['student']->nama,
            ];

            foreach ($this->recap['days'] as $day) {
                $line[] = $row['daily'][$day] ?? '';
            }

            foreach (AttendanceStatus::values() as $status) {
                $line[] = $row['counts'][$status];
            }

            $line[] = $row['percentage'];
            $rows[] = $line;
        }

        return $rows;
    }

    public function headings(): array
    {
        $headings = ['No', 'Nomor Induk Siswa', 'Nama'];

        foreach ($this->recap['days'] as $day) {
            $headings[] = (string) $day;
        }

        foreach (AttendanceStatus::cases() as $status) {
            $headings[] = $status->label();
        }

        $headings[] = 'Kehadiran (%)';

        return $headings;
    }

    public function title(): string
    {
        return 'Rekap '.$this->recap['classroom']->name.' '.$this->recap['month']->format('m-Y');
    }
}
