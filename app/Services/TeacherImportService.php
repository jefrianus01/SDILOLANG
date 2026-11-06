<?php

namespace App\Services;

use App\Enums\TeacherStatus;
use App\Imports\RawRowsImport;
use App\Models\Teacher;
use App\Models\TeacherType;
use App\Models\User;
use App\Support\ImportResult;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class TeacherImportService
{
    public function __construct(private readonly TeacherService $teachers)
    {
    }

    /**
     * Import guru dari Excel. Baris tidak valid dilewati dan dilaporkan nomor barisnya.
     */
    public function import(UploadedFile $file, User $actor): ImportResult
    {
        $rows = Excel::toArray(new RawRowsImport, $file)[0] ?? [];

        $types = TeacherType::query()
            ->where('is_active', true)
            ->get()
            ->keyBy(fn (TeacherType $type) => mb_strtolower(trim($type->name)));

        $result = new ImportResult;
        $seenNip = [];

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // baris 1 = heading

            if ($this->isEmptyRow($row)) {
                continue;
            }

            $nama = trim((string) ($row['nama'] ?? ''));
            $nip = $this->normalizeDigits($row['nip_nik'] ?? null);
            $jenis = trim((string) ($row['jenis_guru'] ?? ''));
            $status = mb_strtolower(trim((string) ($row['status'] ?? ''))) ?: TeacherStatus::Aktif->value;

            $errors = [];

            if ($nama === '') {
                $errors[] = 'Nama wajib diisi.';
            } elseif (mb_strlen($nama) > 150) {
                $errors[] = 'Nama maksimal 150 karakter.';
            }

            if ($nip === '') {
                $errors[] = 'NIP/NIK wajib diisi.';
            } elseif (! preg_match('/^\d{16,18}$/', $nip)) {
                $errors[] = 'NIP/NIK harus berupa 16-18 digit angka.';
            } elseif (isset($seenNip[$nip])) {
                $errors[] = 'NIP/NIK duplikat di dalam file (sudah ada di baris '.$seenNip[$nip].').';
            } elseif (Teacher::withTrashed()->where('nip_nik', $nip)->exists()) {
                $errors[] = 'NIP/NIK sudah terdaftar di database.';
            }

            $teacherType = $jenis === '' ? null : ($types[mb_strtolower($jenis)] ?? null);

            if ($jenis === '') {
                $errors[] = 'Jenis guru wajib diisi.';
            } elseif (! $teacherType) {
                $errors[] = 'Jenis guru "'.$jenis.'" tidak ditemukan pada master data.';
            }

            if (! in_array($status, TeacherStatus::values(), true)) {
                $errors[] = 'Status harus aktif atau nonaktif.';
            }

            if ($errors !== []) {
                $result->failures[] = [
                    'row' => $rowNumber,
                    'messages' => $errors,
                    'data' => ['nama' => $nama, 'nip_nik' => $nip, 'jenis_guru' => $jenis, 'status' => $status],
                ];

                continue;
            }

            $seenNip[$nip] = $rowNumber;

            try {
                [$teacher, $password] = $this->teachers->create([
                    'nama' => $nama,
                    'nip_nik' => $nip,
                    'teacher_type_id' => $teacherType->id,
                    'status' => $status,
                ]);

                $result->imported++;
                $result->accounts[] = [
                    'name' => $teacher->nama,
                    'username' => $teacher->nip_nik,
                    'password' => $password,
                ];
            } catch (\Throwable $e) {
                report($e);
                $result->failures[] = [
                    'row' => $rowNumber,
                    'messages' => ['Gagal menyimpan baris ini: '.$e->getMessage()],
                    'data' => ['nama' => $nama, 'nip_nik' => $nip, 'jenis_guru' => $jenis, 'status' => $status],
                ];
            }
        }

        AuditLogger::log('teacher.imported', null, 'Import guru: '.$result->imported.' berhasil, '.$result->failedCount().' gagal.');

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

    private function normalizeDigits(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        if (is_float($value) || is_int($value)) {
            $value = number_format((float) $value, 0, '', '');
        }

        return preg_replace('/[^0-9]/', '', (string) $value) ?? '';
    }
}
