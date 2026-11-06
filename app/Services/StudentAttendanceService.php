<?php

namespace App\Services;

use App\Enums\AttendanceStatus;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StudentAttendanceService
{
    /**
     * Simpan absensi harian satu kelas sekaligus.
     *
     * @param  array<int|string, string>  $statuses  [student_id => status]
     */
    public function saveForClass(Classroom $classroom, string $date, array $statuses, User $recorder): int
    {
        $day = Carbon::parse($date)->toDateString();

        if ($day > now()->toDateString()) {
            throw ValidationException::withMessages([
                'date' => 'Absensi tidak boleh diisi untuk tanggal yang akan datang.',
            ]);
        }

        $allowedStatuses = AttendanceStatus::values();
        $studentIds = array_map('intval', array_keys($statuses));

        $students = Student::query()
            ->where('classroom_id', $classroom->id)
            ->whereIn('id', $studentIds)
            ->get()
            ->keyBy('id');

        if ($students->isEmpty()) {
            throw ValidationException::withMessages([
                'students' => 'Tidak ada siswa yang dapat disimpan pada kelas ini.',
            ]);
        }

        $saved = 0;

        DB::transaction(function () use ($students, $statuses, $day, $classroom, $recorder, $allowedStatuses, &$saved) {
            foreach ($students as $student) {
                $status = $statuses[$student->id] ?? null;

                if (! in_array($status, $allowedStatuses, true)) {
                    throw ValidationException::withMessages([
                        'status.'.$student->id => 'Status absensi tidak valid untuk '.$student->nama.'.',
                    ]);
                }

                StudentAttendance::updateOrCreate(
                    ['student_id' => $student->id, 'date' => $day],
                    [
                        'classroom_id' => $classroom->id,
                        'status' => $status,
                        'recorded_by' => $recorder->id,
                    ]
                );

                $saved++;
            }
        });

        AuditLogger::log('student-attendance.saved', $classroom, 'Absensi kelas '.$classroom->name.' tanggal '.$day.' ('.$saved.' siswa)');

        return $saved;
    }
}
