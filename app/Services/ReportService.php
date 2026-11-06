<?php

namespace App\Services;

use App\Enums\ApprovalStatus;
use App\Enums\AttendanceStatus;
use App\Enums\TeacherAttendanceStatus;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\Teacher;
use App\Models\TeacherAttendance;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class ReportService
{
    public function parseMonth(string $month): Carbon
    {
        try {
            $date = Carbon::createFromFormat('!Y-m', $month);
        } catch (\Throwable) {
            $date = null;
        }

        if (! $date) {
            throw ValidationException::withMessages([
                'month' => 'Format bulan tidak valid (contoh: 2026-10).',
            ]);
        }

        return $date->startOfMonth();
    }

    /**
     * Rekap absensi siswa per kelas dalam satu bulan.
     *
     * @return array<string, mixed>
     */
    public function classRecap(Classroom $classroom, Carbon $month): array
    {
        $start = $month->copy()->startOfMonth();
        $end = $month->copy()->endOfMonth();

        $students = Student::query()
            ->where('classroom_id', $classroom->id)
            ->where('status', 'aktif')
            ->orderBy('nama')
            ->get();

        $records = StudentAttendance::query()
            ->where('classroom_id', $classroom->id)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->get();

        $map = [];
        foreach ($records as $record) {
            $map[$record->student_id][(int) $record->date->format('j')] = $record->status->value;
        }

        $days = range(1, $start->daysInMonth);
        $rows = [];
        $totals = array_fill_keys(AttendanceStatus::values(), 0);

        foreach ($students as $index => $student) {
            $counts = array_fill_keys(AttendanceStatus::values(), 0);
            $daily = [];

            foreach ($days as $day) {
                $status = $map[$student->id][$day] ?? null;
                $daily[$day] = $status;

                if ($status !== null) {
                    $counts[$status]++;
                    $totals[$status]++;
                }
            }

            $recorded = array_sum($counts);

            $rows[] = [
                'no' => $index + 1,
                'student' => $student,
                'daily' => $daily,
                'counts' => $counts,
                'recorded' => $recorded,
                'percentage' => $recorded > 0 ? round(($counts[AttendanceStatus::Hadir->value] / $recorded) * 100, 1) : 0.0,
            ];
        }

        return [
            'classroom' => $classroom,
            'month' => $start,
            'days' => $days,
            'rows' => $rows,
            'totals' => $totals,
            'recorded' => array_sum($totals),
        ];
    }

    /**
     * Rekap absensi guru dalam satu bulan.
     *
     * @return array<string, mixed>
     */
    public function teacherRecap(Carbon $month): array
    {
        $start = $month->copy()->startOfMonth();
        $end = $month->copy()->endOfMonth();

        $teachers = Teacher::query()
            ->with('teacherType')
            ->where('status', 'aktif')
            ->orderBy('nama')
            ->get();

        $records = TeacherAttendance::query()
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->get()
            ->groupBy('teacher_id');

        $rows = [];
        $totals = array_fill_keys(TeacherAttendanceStatus::values(), 0);
        $totals['pending'] = 0;

        foreach ($teachers as $index => $teacher) {
            $counts = array_fill_keys(TeacherAttendanceStatus::values(), 0);
            $pending = 0;

            foreach ($records->get($teacher->id, collect()) as $record) {
                if ($record->approval_status === ApprovalStatus::Rejected) {
                    continue;
                }

                if ($record->approval_status === ApprovalStatus::Pending) {
                    $pending++;
                }

                $counts[$record->status->value]++;
                $totals[$record->status->value]++;
            }

            $totals['pending'] += $pending;

            $rows[] = [
                'no' => $index + 1,
                'teacher' => $teacher,
                'counts' => $counts,
                'pending' => $pending,
                'recorded' => array_sum($counts),
            ];
        }

        return [
            'month' => $start,
            'days' => $start->daysInMonth,
            'rows' => $rows,
            'totals' => $totals,
        ];
    }
}
