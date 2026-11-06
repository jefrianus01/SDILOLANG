<?php

namespace App\Services;

use App\Enums\ApprovalStatus;
use App\Enums\TeacherAttendanceStatus;
use App\Models\Setting;
use App\Models\TeacherAttendance;
use App\Models\User;
use App\Support\Geo;
use App\Support\SchoolSetting;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TeacherAttendanceService
{
    public function todayFor(User $user): ?TeacherAttendance
    {
        if (! $user->teacher) {
            return null;
        }

        return TeacherAttendance::query()
            ->where('teacher_id', $user->teacher->id)
            ->whereDate('date', now()->toDateString())
            ->first();
    }

    public function checkIn(User $user, float $lat, float $lng): TeacherAttendance
    {
        $teacher = $this->activeTeacher($user);
        $date = now()->toDateString();

        $existing = TeacherAttendance::query()
            ->where('teacher_id', $teacher->id)
            ->whereDate('date', $date)
            ->first();

        if ($existing && $existing->check_in_at) {
            throw ValidationException::withMessages([
                'attendance' => 'Anda sudah melakukan absen masuk hari ini.',
            ]);
        }

        if ($existing && $existing->status->isPermission()
            && in_array($existing->approval_status, [ApprovalStatus::Pending, ApprovalStatus::Approved], true)) {
            throw ValidationException::withMessages([
                'attendance' => 'Hari ini Anda sudah mengajukan izin/dinas luar.',
            ]);
        }

        $distance = $this->distanceToSchool($lat, $lng);

        $status = now()->format('H:i') > SchoolSetting::lateAfter()
            ? TeacherAttendanceStatus::Terlambat
            : TeacherAttendanceStatus::Hadir;

        $attendance = TeacherAttendance::updateOrCreate(
            ['teacher_id' => $teacher->id, 'date' => $date],
            [
                'check_in_at' => now(),
                'check_in_lat' => $lat,
                'check_in_lng' => $lng,
                'check_in_distance' => $distance,
                'status' => $status->value,
                'approval_status' => ApprovalStatus::NotRequired->value,
                'reason' => null,
                'approved_by' => null,
                'approved_at' => null,
                'approval_note' => null,
            ]
        );

        AuditLogger::log('teacher-attendance.check-in', $attendance, 'Absen masuk ('.$distance.' m)');

        return $attendance;
    }

    public function checkOut(User $user, float $lat, float $lng): TeacherAttendance
    {
        $teacher = $this->activeTeacher($user);
        $date = now()->toDateString();

        $attendance = TeacherAttendance::query()
            ->where('teacher_id', $teacher->id)
            ->whereDate('date', $date)
            ->first();

        if (! $attendance || ! $attendance->check_in_at) {
            throw ValidationException::withMessages([
                'attendance' => 'Anda belum melakukan absen masuk hari ini.',
            ]);
        }

        if ($attendance->check_out_at) {
            throw ValidationException::withMessages([
                'attendance' => 'Anda sudah melakukan absen pulang hari ini.',
            ]);
        }

        $distance = $this->distanceToSchool($lat, $lng);

        $attendance->update([
            'check_out_at' => now(),
            'check_out_lat' => $lat,
            'check_out_lng' => $lng,
            'check_out_distance' => $distance,
        ]);

        AuditLogger::log('teacher-attendance.check-out', $attendance, 'Absen pulang ('.$distance.' m)');

        return $attendance->refresh();
    }

    /**
     * @param  array{date: string, type: string, reason: string, attachment?: UploadedFile|null}  $data
     */
    public function requestPermission(User $user, array $data): TeacherAttendance
    {
        $teacher = $this->activeTeacher($user);
        $date = Carbon::parse($data['date'])->toDateString();

        if ($date > now()->toDateString()) {
            throw ValidationException::withMessages([
                'date' => 'Tanggal izin tidak boleh di masa depan.',
            ]);
        }

        $type = TeacherAttendanceStatus::tryFrom($data['type']);

        if (! $type || ! $type->isPermission()) {
            throw ValidationException::withMessages([
                'type' => 'Jenis pengajuan tidak valid.',
            ]);
        }

        $existing = TeacherAttendance::query()
            ->where('teacher_id', $teacher->id)
            ->whereDate('date', $date)
            ->first();

        if ($existing && $existing->check_in_at) {
            throw ValidationException::withMessages([
                'date' => 'Anda sudah absen masuk pada tanggal tersebut.',
            ]);
        }

        if ($existing && $existing->status->isPermission()
            && in_array($existing->approval_status, [ApprovalStatus::Pending, ApprovalStatus::Approved], true)) {
            throw ValidationException::withMessages([
                'date' => 'Sudah ada pengajuan izin pada tanggal tersebut.',
            ]);
        }

        $attachmentPath = $data['attachment'] ?? null
            ? $data['attachment']->store('attendance-attachments', 'public')
            : null;

        $attendance = TeacherAttendance::updateOrCreate(
            ['teacher_id' => $teacher->id, 'date' => $date],
            [
                'check_in_at' => null,
                'check_in_lat' => null,
                'check_in_lng' => null,
                'check_in_distance' => null,
                'status' => $type->value,
                'approval_status' => ApprovalStatus::Pending->value,
                'reason' => $data['reason'],
                'attachment_path' => $attachmentPath,
                'approved_by' => null,
                'approved_at' => null,
                'approval_note' => null,
            ]
        );

        AuditLogger::log('teacher-attendance.permission-requested', $attendance, 'Pengajuan '.$type->label());

        return $attendance;
    }

    public function decide(TeacherAttendance $attendance, User $approver, bool $approve, ?string $note = null): TeacherAttendance
    {
        if (! $attendance->isPending()) {
            throw ValidationException::withMessages([
                'attendance' => 'Pengajuan ini sudah diproses.',
            ]);
        }

        DB::transaction(function () use ($attendance, $approver, $approve, $note) {
            $attendance->update([
                'approval_status' => $approve ? ApprovalStatus::Approved->value : ApprovalStatus::Rejected->value,
                'approved_by' => $approver->id,
                'approved_at' => now(),
                'approval_note' => $note,
                'status' => $approve ? $attendance->status->value : TeacherAttendanceStatus::Alpa->value,
            ]);
        });

        AuditLogger::log(
            $approve ? 'teacher-attendance.approved' : 'teacher-attendance.rejected',
            $attendance,
            ($approve ? 'Menyetujui' : 'Menolak').' pengajuan '.$attendance->teacher?->nama
        );

        return $attendance->refresh();
    }

    private function activeTeacher(User $user)
    {
        $teacher = $user->teacher;

        if (! $teacher) {
            throw ValidationException::withMessages([
                'attendance' => 'Akun Anda belum terhubung dengan data guru.',
            ]);
        }

        if (! $teacher->isAktif()) {
            throw ValidationException::withMessages([
                'attendance' => 'Status guru Anda nonaktif, absensi tidak dapat dilakukan.',
            ]);
        }

        return $teacher;
    }

    private function distanceToSchool(float $lat, float $lng): int
    {
        if (! Geo::isValidCoordinate($lat, $lng)) {
            throw ValidationException::withMessages([
                'location' => 'Koordinat lokasi tidak valid.',
            ]);
        }

        if (! SchoolSetting::hasCoordinates()) {
            throw ValidationException::withMessages([
                'location' => 'Koordinat sekolah belum diatur. Hubungi Kepala Sekolah.',
            ]);
        }

        $distance = (int) round(Geo::distanceMeters(
            $lat,
            $lng,
            (float) SchoolSetting::latitude(),
            (float) SchoolSetting::longitude(),
        ));

        if ($distance > SchoolSetting::radius()) {
            throw ValidationException::withMessages([
                'location' => 'Lokasi Anda berada '.$distance.' m dari sekolah. Maksimal '.SchoolSetting::radius().' m.',
            ]);
        }

        return $distance;
    }
}
