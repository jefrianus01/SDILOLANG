<?php

namespace Tests\Feature;

use App\Models\Classroom;
use App\Models\Student;
use App\Support\Roles;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesSchoolData;
use Tests\TestCase;

class StudentManagementTest extends TestCase
{
    use CreatesSchoolData, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedRoles();
    }

    public function test_wali_kelas_can_add_student_to_own_class(): void
    {
        [$wali, $teacher] = $this->teacherUser(Roles::WALI_KELAS);
        $classroom = $this->classroomFor($teacher);

        $this->actingAs($wali)->post(route('students.store'), [
            'nomor_induk_siswa' => '2026001',
            'nama' => 'Siswa Baru',
        ])->assertRedirect(route('students.index'));

        $this->assertDatabaseHas('students', [
            'nomor_induk_siswa' => '2026001',
            'nama' => 'Siswa Baru',
            'classroom_id' => $classroom->id,
            'status' => 'aktif',
        ]);
    }

    public function test_duplicate_nomor_induk_siswa_is_rejected(): void
    {
        [$wali, $teacher] = $this->teacherUser(Roles::WALI_KELAS);
        $classroom = $this->classroomFor($teacher);
        $this->studentIn($classroom, ['nomor_induk_siswa' => '2026001']);

        $this->actingAs($wali)->post(route('students.store'), [
            'nomor_induk_siswa' => '2026001',
            'nama' => 'Siswa Duplikat',
        ])->assertSessionHasErrors('nomor_induk_siswa');

        $this->assertSame(1, Student::withTrashed()->where('nomor_induk_siswa', '2026001')->count());
    }

    public function test_wali_kelas_cannot_access_students_of_another_class(): void
    {
        [$waliA, $teacherA] = $this->teacherUser(Roles::WALI_KELAS);
        [$waliB, $teacherB] = $this->teacherUser(Roles::WALI_KELAS);

        $classA = $this->classroomFor($teacherA);
        $classB = $this->classroomFor($teacherB);
        $studentB = $this->studentIn($classB, ['nama' => 'Siswa Kelas B']);

        $this->actingAs($waliA)->get(route('students.edit', $studentB))->assertForbidden();

        $this->actingAs($waliA)->put(route('students.update', $studentB), [
            'nomor_induk_siswa' => $studentB->nomor_induk_siswa,
            'nama' => 'Diubah Ilegal',
        ])->assertForbidden();

        $this->actingAs($waliA)->delete(route('students.destroy', $studentB))->assertForbidden();

        $this->assertSame('Siswa Kelas B', $studentB->refresh()->nama);
        $this->assertSame($classB->id, $studentB->classroom_id);
    }

    public function test_student_index_only_lists_own_class_students(): void
    {
        [$waliA, $teacherA] = $this->teacherUser(Roles::WALI_KELAS);
        [$waliB, $teacherB] = $this->teacherUser(Roles::WALI_KELAS);

        $classA = $this->classroomFor($teacherA);
        $classB = $this->classroomFor($teacherB);
        $this->studentIn($classA, ['nama' => 'Siswa Milik A']);
        $this->studentIn($classB, ['nama' => 'Siswa Milik B']);

        $this->actingAs($waliA)->get(route('students.index'))
            ->assertOk()
            ->assertSee('Siswa Milik A')
            ->assertDontSee('Siswa Milik B');
    }

    public function test_kepala_sekolah_can_view_but_not_create_students(): void
    {
        $headmaster = $this->userWithRole(Roles::KEPALA_SEKOLAH);
        [$wali, $teacher] = $this->teacherUser(Roles::WALI_KELAS);
        $classroom = $this->classroomFor($teacher);
        $this->studentIn($classroom, ['nama' => 'Siswa Terlihat']);

        $this->actingAs($headmaster)->get(route('students.index'))
            ->assertOk()
            ->assertSee('Siswa Terlihat');

        $this->actingAs($headmaster)->get(route('students.create'))->assertForbidden();
        $this->actingAs($headmaster)->post(route('students.store'), [
            'nomor_induk_siswa' => '2026999',
            'nama' => 'Tidak Boleh',
        ])->assertForbidden();
    }

    public function test_super_admin_cannot_create_students(): void
    {
        $superAdmin = $this->userWithRole(Roles::SUPER_ADMIN);

        $this->actingAs($superAdmin)->get(route('students.create'))->assertForbidden();
    }

    public function test_wali_kelas_without_class_cannot_create_students(): void
    {
        [$wali] = $this->teacherUser(Roles::WALI_KELAS);

        $this->actingAs($wali)->get(route('students.create'))->assertForbidden();
    }

    public function test_only_super_admin_can_promote_students(): void
    {
        $headmaster = $this->userWithRole(Roles::KEPALA_SEKOLAH);
        $superAdmin = $this->userWithRole(Roles::SUPER_ADMIN);

        $this->actingAs($headmaster)->get(route('students.promote.form'))->assertForbidden();
        $this->actingAs($superAdmin)->get(route('students.promote.form'))->assertOk();
    }

    public function test_promotion_moves_students_to_target_classroom(): void
    {
        $superAdmin = $this->userWithRole(Roles::SUPER_ADMIN);
        $year = $this->activeYear();

        $source = Classroom::factory()->create(['academic_year_id' => $year->id, 'name' => 'Kelas 1', 'level' => 1]);
        $target = Classroom::factory()->create(['academic_year_id' => $year->id, 'name' => 'Kelas 2', 'level' => 2]);

        $student = $this->studentIn($source);

        $this->actingAs($superAdmin)->post(route('students.promote'), [
            'source_classroom_id' => $source->id,
            'target_classroom_id' => $target->id,
        ])->assertRedirect(route('students.promote.form'));

        $this->assertSame($target->id, $student->refresh()->classroom_id);
    }

    public function test_promotion_without_target_marks_students_as_graduated(): void
    {
        $superAdmin = $this->userWithRole(Roles::SUPER_ADMIN);
        $year = $this->activeYear();
        $source = Classroom::factory()->create(['academic_year_id' => $year->id, 'name' => 'Kelas 6', 'level' => 6]);
        $student = $this->studentIn($source);

        $this->actingAs($superAdmin)->post(route('students.promote'), [
            'source_classroom_id' => $source->id,
            'target_classroom_id' => null,
        ])->assertRedirect(route('students.promote.form'));

        $this->assertSame('lulus', $student->refresh()->status->value);
    }

    public function test_student_export_requires_access(): void
    {
        [$guru] = $this->teacherUser(Roles::GURU);

        $this->actingAs($guru)->get(route('students.index'))->assertForbidden();
        $this->actingAs($guru)->get(route('students.export.pdf'))->assertForbidden();
    }
}
