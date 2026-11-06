<?php

namespace App\Services;

use App\Imports\RawRowsImport;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\User;
use App\Support\ImportResult;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class StudentImportService
{
    /**
     * Import siswa ke satu kelas. Baris tidak valid dilewati & dilaporkan.
     */
    public function import(UploadedFile $file, Classroom $classroom, User $actor): ImportResult
    {
        $rows = Excel::toArray(new RawRowsImport, $file)[0] ?? [];

        $result = new ImportResult;
        $seenNis = [];

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;

            if ($this->isEmptyRow($row)) {
                continue;
            }

            $nis = trim((string) ($row['nomor_induk_siswa'] ?? ''));
            $nama = trim((string) ($row['nama'] ?? ''));

            if (is_float($row['nomor_induk_siswa'] ?? null) || is_int($row['nomor_induk_siswa'] ?? null)) {
                $nis = number_format((float) $row['nomor_induk_siswa'], 0, '', '');
            }

            $errors = [];

            if ($nis === '') {
                $errors[] = 'Nomor Induk Siswa wajib diisi.';
            } elseif (mb_strlen($nis) > 30) {
                $errors[] = 'Nomor Induk Siswa maksimal 30 karakter.';
            } elseif (isset($seenNis[$nis])) {
                $errors[] = 'Nomor Induk Siswa duplikat di dalam file (sudah ada di baris '.$seenNis[$nis].').';
            } elseif (Student::withTrashed()->where('nomor_induk_siswa', $nis)->exists()) {
                $errors[] = 'Nomor Induk Siswa sudah terdaftar di database.';
            }

            if ($nama === '') {
                $errors[] = 'Nama wajib diisi.';
            } elseif (mb_strlen($nama) > 150) {
                $errors[] = 'Nama maksimal 150 karakter.';
            }

            if ($errors !== []) {
                $result->failures[] = [
                    'row' => $rowNumber,
                    'messages' => $errors,
                    'data' => ['nomor_induk_siswa' => $nis, 'nama' => $nama],
                ];

                continue;
            }

            $seenNis[$nis] = $rowNumber;

            try {
                DB::transaction(function () use ($classroom, $nis, $nama) {
                    Student::create([
                        'nomor_induk_siswa' => $nis,
                        'nama' => $nama,
                        'classroom_id' => $classroom->id,
                        'status' => 'aktif',
                    ]);
                });

                $result->imported++;
            } catch (\Throwable $e) {
                report($e);
                $result->failures[] = [
                    'row' => $rowNumber,
                    'messages' => ['Gagal menyimpan baris ini: '.$e->getMessage()],
                    'data' => ['nomor_induk_siswa' => $nis, 'nama' => $nama],
                ];
            }
        }

        AuditLogger::log('student.imported', $classroom, 'Import siswa kelas '.$classroom->name.': '.$result->imported.' berhasil, '.$result->failedCount().' gagal.');

        return $result;
    }

    private function isEmptyRow(array $row): bool
    {
        foreach ($row as $value) {
            if ($value !== null && trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }
}
