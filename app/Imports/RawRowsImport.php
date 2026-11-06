<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithHeadingRow;

/**
 * Import mentah: seluruh baris dibaca sebagai array dengan key heading.
 * Validasi & pelaporan nomor baris dilakukan manual di service.
 */
class RawRowsImport implements WithHeadingRow
{
}
