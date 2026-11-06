<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class InstructionsSheet implements FromArray, ShouldAutoSize, WithHeadings, WithTitle
{
    /**
     * @param  array<int, string>  $lines
     */
    public function __construct(
        private readonly string $sheetTitle,
        private readonly array $lines,
    ) {
    }

    public function array(): array
    {
        return array_map(fn (string $line) => [$line], $this->lines);
    }

    public function headings(): array
    {
        return ['Petunjuk'];
    }

    public function title(): string
    {
        return $this->sheetTitle;
    }
}
