<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\Teacher;
use App\Support\Roles;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesSchoolData;
use Tests\TestCase;

class ImportTest extends TestCase
{
    use CreatesSchoolData, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedRoles();
    }

    public function test_teacher_import_reports_invalid_rows_with_row_numbers(): void
    {
        $headmaster = $this->userWithRole(Roles::KEPALA_SEKOLAH);
        $this->teacherType('Guru Kelas');

        $file = $this->makeXlsx([
            ['nama', 'nip_nik', 'jenis_guru', 'status'],
            ['Guru Valid', '198001012005011001', 'Guru Kelas', 'aktif'],
            ['NIP Salah', '123', 'Guru Kelas', 'aktif'],
            ['Jenis Salah', '198001012005011002', 'Guru Tidak Ada', 'aktif'],
            ['Status Salah', '198001012005011003', 'Guru Kelas', 'kadang'],
        ]);

        $response = $this->actingAs($headmaster)->post(route('teachers.import'), ['file' => $file]);

        $response->assertRedirect(route('teachers.import.form'));
        $response->assertSessionHas('import_result');

        $result = $response->getSession()->get('import_result');

        $this->assertSame(1, $result['imported']);
        $this->assertCount(3, $result['failures']);

        $rows = array_column($result['failures'], 'row');
        $this->assertSame([3, 4, 5], $rows);

        $messages = collect($result['failures'])->flatMap(fn ($failure) => $failure['messages'])->implode(' ');
        $this->assertStringContainsString('16-18 digit', $messages);
        $this->assertStringContainsString('tidak ditemukan', $messages);
        $this->assertStringContainsString('Status harus aktif atau nonaktif', $messages);

        $this->assertSame(1, Teacher::withTrashed()->where('nip_nik', '198001012005011001')->count());
        $this->assertCount(1, $result['accounts']);
    }

    public function test_teacher_import_rejects_duplicate_nip_in_file_and_database(): void
    {
        $headmaster = $this->userWithRole(Roles::KEPALA_SEKOLAH);
        $this->teacherType('Guru Kelas');
        Teacher::factory()->create(['nip_nik' => '198001012005011009']);

        $file = $this->makeXlsx([
            ['nama', 'nip_nik', 'jenis_guru', 'status'],
            ['Duplikat File 1', '198001012005011010', 'Guru Kelas', 'aktif'],
            ['Duplikat File 2', '198001012005011010', 'Guru Kelas', 'aktif'],
            ['Duplikat DB', '198001012005011009', 'Guru Kelas', 'aktif'],
        ]);

        $response = $this->actingAs($headmaster)->post(route('teachers.import'), ['file' => $file]);
        $result = $response->getSession()->get('import_result');

        $this->assertSame(1, $result['imported']);
        $this->assertSame([3, 4], array_column($result['failures'], 'row'));

        $messages = collect($result['failures'])->flatMap(fn ($failure) => $failure['messages'])->implode(' ');
        $this->assertStringContainsString('duplikat di dalam file', $messages);
        $this->assertStringContainsString('sudah terdaftar', $messages);
    }

    public function test_student_import_assigns_classroom_and_reports_invalid_rows(): void
    {
        [$wali, $teacher] = $this->teacherUser(Roles::WALI_KELAS);
        $classroom = $this->classroomFor($teacher);

        $file = $this->makeXlsx([
            ['nomor_induk_siswa', 'nama'],
            ['2026001', 'Siswa Valid'],
            ['', 'Tanpa NIS'],
            ['2026001', 'Duplikat'],
        ]);

        $response = $this->actingAs($wali)->post(route('students.import'), ['file' => $file]);

        $response->assertRedirect(route('students.import.form'));
        $result = $response->getSession()->get('import_result');

        $this->assertSame(1, $result['imported']);
        $this->assertSame([3, 4], array_column($result['failures'], 'row'));

        $this->assertDatabaseHas('students', [
            'nomor_induk_siswa' => '2026001',
            'nama' => 'Siswa Valid',
            'classroom_id' => $classroom->id,
        ]);
    }

    public function test_student_import_rejects_nis_already_in_database(): void
    {
        [$wali, $teacher] = $this->teacherUser(Roles::WALI_KELAS);
        $classroom = $this->classroomFor($teacher);
        $this->studentIn($classroom, ['nomor_induk_siswa' => '2026002']);

        $file = $this->makeXlsx([
            ['nomor_induk_siswa', 'nama'],
            ['2026002', 'Siswa Duplikat'],
        ]);

        $response = $this->actingAs($wali)->post(route('students.import'), ['file' => $file]);
        $result = $response->getSession()->get('import_result');

        $this->assertSame(0, $result['imported']);
        $this->assertSame([2], array_column($result['failures'], 'row'));
        $this->assertSame(1, Student::withTrashed()->where('nomor_induk_siswa', '2026002')->count());
    }

    public function test_guru_cannot_import_students_or_teachers(): void
    {
        [$guru] = $this->teacherUser(Roles::GURU);

        $file = $this->makeXlsx([
            ['nomor_induk_siswa', 'nama'],
            ['2026003', 'Siswa'],
        ]);

        $this->actingAs($guru)->get(route('students.import.form'))->assertForbidden();
        $this->actingAs($guru)->post(route('students.import'), ['file' => $file])->assertForbidden();
        $this->actingAs($guru)->get(route('teachers.import.form'))->assertForbidden();
    }

    public function test_import_requires_a_file(): void
    {
        $headmaster = $this->userWithRole(Roles::KEPALA_SEKOLAH);

        $this->actingAs($headmaster)
            ->post(route('teachers.import'), [])
            ->assertSessionHasErrors('file');
    }
}
