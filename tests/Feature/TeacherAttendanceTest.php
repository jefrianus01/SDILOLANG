<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\TeacherAttendance;
use App\Support\Roles;
use App\Support\SchoolSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesSchoolData;
use Tests\TestCase;

class TeacherAttendanceTest extends TestCase
{
    use CreatesSchoolData, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedRoles();

        Setting::putMany([
            SchoolSetting::LATITUDE => '1.4748000',
            SchoolSetting::LONGITUDE => '124.8421000',
            SchoolSetting::GEOFENCE_RADIUS => '50',
            SchoolSetting::LATE_AFTER => '07:15',
        ], 'school');
    }

    public function test_teacher_can_check_in_inside_geofence(): void
    {
        [$user, $teacher] = $this->teacherUser(Roles::GURU);

        $response = $this->actingAs($user)->post(route('teacher-attendance.check-in'), [
            'latitude' => 1.4748,
            'longitude' => 124.8421,
        ]);

        $response->assertSessionHasNoErrors();

        $attendance = TeacherAttendance::query()->where('teacher_id', $teacher->id)->first();

        $this->assertNotNull($attendance);
        $this->assertNotNull($attendance->check_in_at);
        $this->assertLessThanOrEqual(50, $attendance->check_in_distance);
    }

    public function test_teacher_cannot_check_in_outside_geofence(): void
    {
        [$user, $teacher] = $this->teacherUser(Roles::GURU);

        $response = $this->actingAs($user)->post(route('teacher-attendance.check-in'), [
            'latitude' => 1.6000,
            'longitude' => 124.9000,
        ]);

        $response->assertSessionHasErrors('location');
        $this->assertSame(0, TeacherAttendance::query()->where('teacher_id', $teacher->id)->count());
    }

    public function test_teacher_cannot_check_in_twice_on_the_same_day(): void
    {
        [$user, $teacher] = $this->teacherUser(Roles::GURU);

        $this->actingAs($user)->post(route('teacher-attendance.check-in'), [
            'latitude' => 1.4748,
            'longitude' => 124.8421,
        ])->assertSessionHasNoErrors();

        $this->actingAs($user)->post(route('teacher-attendance.check-in'), [
            'latitude' => 1.4748,
            'longitude' => 124.8421,
        ])->assertSessionHasErrors('attendance');

        $this->assertSame(1, TeacherAttendance::query()->where('teacher_id', $teacher->id)->count());
    }

    public function test_check_in_requires_school_coordinates(): void
    {
        Setting::query()
            ->whereIn('key', [SchoolSetting::LATITUDE, SchoolSetting::LONGITUDE])
            ->get()
            ->each(fn (Setting $setting) => $setting->delete());

        [$user] = $this->teacherUser(Roles::GURU);

        $this->actingAs($user)->post(route('teacher-attendance.check-in'), [
            'latitude' => 1.4748,
            'longitude' => 124.8421,
        ])->assertSessionHasErrors('location');
    }

    public function test_teacher_can_check_out_after_check_in(): void
    {
        [$user, $teacher] = $this->teacherUser(Roles::GURU);

        $this->actingAs($user)->post(route('teacher-attendance.check-in'), [
            'latitude' => 1.4748,
            'longitude' => 124.8421,
        ]);

        $this->actingAs($user)->post(route('teacher-attendance.check-out'), [
            'latitude' => 1.4748,
            'longitude' => 124.8421,
        ])->assertSessionHasNoErrors();

        $this->assertNotNull(
            TeacherAttendance::query()->where('teacher_id', $teacher->id)->first()->check_out_at
        );
    }

    public function test_teacher_can_not_check_out_without_check_in(): void
    {
        [$user] = $this->teacherUser(Roles::GURU);

        $this->actingAs($user)->post(route('teacher-attendance.check-out'), [
            'latitude' => 1.4748,
            'longitude' => 124.8421,
        ])->assertSessionHasErrors('attendance');
    }

    public function test_permission_request_can_be_approved_by_headmaster(): void
    {
        [$user, $teacher] = $this->teacherUser(Roles::GURU);
        $headmaster = $this->userWithRole(Roles::KEPALA_SEKOLAH);

        $this->actingAs($user)->post(route('teacher-attendance.permission'), [
            'date' => now()->subDay()->toDateString(),
            'type' => 'izin',
            'reason' => 'Acara keluarga.',
        ])->assertSessionHasNoErrors();

        $attendance = TeacherAttendance::query()->where('teacher_id', $teacher->id)->first();
        $this->assertSame('pending', $attendance->approval_status->value);
        $this->assertSame('izin', $attendance->status->value);

        $this->actingAs($headmaster)
            ->post(route('teacher-attendance.approve', $attendance), ['note' => 'Disetujui.'])
            ->assertSessionHasNoErrors();

        $attendance->refresh();
        $this->assertSame('approved', $attendance->approval_status->value);
        $this->assertSame($headmaster->id, $attendance->approved_by);
        $this->assertNotNull($attendance->approved_at);
    }

    public function test_rejected_permission_is_recorded_as_absent(): void
    {
        [$user, $teacher] = $this->teacherUser(Roles::GURU);
        $headmaster = $this->userWithRole(Roles::KEPALA_SEKOLAH);

        $this->actingAs($user)->post(route('teacher-attendance.permission'), [
            'date' => now()->subDay()->toDateString(),
            'type' => 'dinas_luar',
            'reason' => 'Tugas luar.',
        ]);

        $attendance = TeacherAttendance::query()->where('teacher_id', $teacher->id)->first();

        $this->actingAs($headmaster)
            ->post(route('teacher-attendance.reject', $attendance), ['note' => 'Tidak ada surat tugas.'])
            ->assertSessionHasNoErrors();

        $attendance->refresh();
        $this->assertSame('rejected', $attendance->approval_status->value);
        $this->assertSame('alpa', $attendance->status->value);
    }

    public function test_only_headmaster_can_access_approvals(): void
    {
        [$user] = $this->teacherUser(Roles::GURU);

        $this->actingAs($user)->get(route('teacher-attendance.approvals'))->assertForbidden();
        $this->actingAs($user)->get(route('teacher-attendance.admin'))->assertForbidden();
    }

    public function test_user_without_teacher_record_cannot_open_self_attendance(): void
    {
        $superAdmin = $this->userWithRole(Roles::SUPER_ADMIN);

        $this->actingAs($superAdmin)->get(route('teacher-attendance.index'))->assertForbidden();
    }
}
