<?php

namespace Tests\Feature;

use App\Models\Classroom;
use App\Models\Teacher;
use App\Models\TeacherType;
use App\Models\User;
use App\Support\Roles;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\Concerns\CreatesSchoolData;
use Tests\TestCase;

class TeacherManagementTest extends TestCase
{
    use CreatesSchoolData, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedRoles();
    }

    public function test_kepala_sekolah_can_create_teacher_with_login_account(): void
    {
        $headmaster = $this->userWithRole(Roles::KEPALA_SEKOLAH);
        $type = $this->teacherType();

        $response = $this->actingAs($headmaster)->post(route('teachers.store'), [
            'nama' => 'Guru Baru, S.Pd.',
            'nip_nik' => '198001012005011001',
            'teacher_type_id' => $type->id,
            'status' => 'aktif',
        ]);

        $response->assertRedirect(route('teachers.index'));
        $response->assertSessionHas('generated_account');

        $this->assertDatabaseHas('teachers', [
            'nama' => 'Guru Baru, S.Pd.',
            'nip_nik' => '198001012005011001',
            'status' => 'aktif',
        ]);

        $user = User::query()->where('username', '198001012005011001')->first();

        $this->assertNotNull($user);
        $this->assertSame('Guru Baru, S.Pd.', $user->name);
        $this->assertTrue($user->must_change_password);
        $this->assertTrue($user->is_active);
        $this->assertTrue($user->hasRole(Roles::GURU));

        $account = $response->getSession()->get('generated_account');
        $this->assertSame('198001012005011001', $account['username']);
        $this->assertTrue(Hash::check($account['password'], $user->password));
    }

    public function test_super_admin_can_create_teacher_as_backup(): void
    {
        $superAdmin = $this->userWithRole(Roles::SUPER_ADMIN);
        $type = $this->teacherType();

        $this->actingAs($superAdmin)->post(route('teachers.store'), [
            'nama' => 'Guru Cadangan',
            'nip_nik' => '198001012005011099',
            'teacher_type_id' => $type->id,
            'status' => 'aktif',
        ])->assertRedirect(route('teachers.index'));

        $this->assertDatabaseHas('teachers', ['nip_nik' => '198001012005011099']);
    }

    public function test_guru_cannot_manage_teachers(): void
    {
        [$guru] = $this->teacherUser(Roles::GURU);
        $type = $this->teacherType();

        $this->actingAs($guru)->get(route('teachers.index'))->assertForbidden();
        $this->actingAs($guru)->get(route('teachers.create'))->assertForbidden();

        $this->actingAs($guru)->post(route('teachers.store'), [
            'nama' => 'Guru Ilegal',
            'nip_nik' => '198001012005011002',
            'teacher_type_id' => $type->id,
            'status' => 'aktif',
        ])->assertForbidden();

        $this->assertDatabaseMissing('teachers', ['nip_nik' => '198001012005011002']);
    }

    public function test_wali_kelas_cannot_manage_teachers(): void
    {
        [$wali] = $this->teacherUser(Roles::WALI_KELAS);
        $other = Teacher::factory()->create();

        $this->actingAs($wali)->get(route('teachers.index'))->assertForbidden();
        $this->actingAs($wali)->get(route('teachers.edit', $other))->assertForbidden();
        $this->actingAs($wali)->delete(route('teachers.destroy', $other))->assertForbidden();
    }

    public function test_duplicate_nip_nik_is_rejected(): void
    {
        $headmaster = $this->userWithRole(Roles::KEPALA_SEKOLAH);
        $type = $this->teacherType();
        Teacher::factory()->create(['nip_nik' => '198001012005011003']);

        $response = $this->actingAs($headmaster)->post(route('teachers.store'), [
            'nama' => 'Duplikat',
            'nip_nik' => '198001012005011003',
            'teacher_type_id' => $type->id,
            'status' => 'aktif',
        ]);

        $response->assertSessionHasErrors('nip_nik');
        $this->assertSame(1, Teacher::withTrashed()->where('nip_nik', '198001012005011003')->count());
    }

    public function test_invalid_nip_nik_format_is_rejected(): void
    {
        $headmaster = $this->userWithRole(Roles::KEPALA_SEKOLAH);
        $type = $this->teacherType();

        $this->actingAs($headmaster)->post(route('teachers.store'), [
            'nama' => 'NIP Pendek',
            'nip_nik' => '12345',
            'teacher_type_id' => $type->id,
            'status' => 'aktif',
        ])->assertSessionHasErrors('nip_nik');
    }

    public function test_updating_teacher_status_deactivates_login(): void
    {
        $headmaster = $this->userWithRole(Roles::KEPALA_SEKOLAH);
        [$teacherUser, $teacher] = $this->teacherUser(Roles::GURU);

        $this->actingAs($headmaster)->put(route('teachers.update', $teacher), [
            'nama' => 'Nama Baru',
            'nip_nik' => $teacher->nip_nik,
            'teacher_type_id' => $teacher->teacher_type_id,
            'status' => 'nonaktif',
        ])->assertRedirect(route('teachers.index'));

        $this->assertSame('Nama Baru', $teacher->refresh()->nama);
        $this->assertFalse($teacherUser->refresh()->is_active);
    }

    public function test_deleting_teacher_soft_deletes_and_releases_homeroom(): void
    {
        $headmaster = $this->userWithRole(Roles::KEPALA_SEKOLAH);
        [$homeroomUser, $teacher] = $this->teacherUser(Roles::WALI_KELAS);
        $classroom = $this->classroomFor($teacher);

        $this->actingAs($headmaster)
            ->delete(route('teachers.destroy', $teacher))
            ->assertRedirect(route('teachers.index'));

        $this->assertSoftDeleted('teachers', ['id' => $teacher->id]);
        $this->assertFalse($homeroomUser->refresh()->is_active);
        $this->assertNull(Classroom::find($classroom->id)->wali_kelas_id);
    }

    public function test_reset_password_shows_new_password_once(): void
    {
        $headmaster = $this->userWithRole(Roles::KEPALA_SEKOLAH);
        [$teacherUser, $teacher] = $this->teacherUser(Roles::GURU);

        $response = $this->actingAs($headmaster)
            ->post(route('teachers.reset-password', $teacher));

        $response->assertSessionHas('generated_account');
        $account = $response->getSession()->get('generated_account');

        $teacherUser->refresh();
        $this->assertTrue(Hash::check($account['password'], $teacherUser->password));
        $this->assertTrue($teacherUser->must_change_password);
    }

    public function test_teacher_type_is_required_when_creating_teacher(): void
    {
        $headmaster = $this->userWithRole(Roles::KEPALA_SEKOLAH);

        $this->actingAs($headmaster)->post(route('teachers.store'), [
            'nama' => 'Tanpa Jenis',
            'nip_nik' => '198001012005011004',
            'teacher_type_id' => 99999,
            'status' => 'aktif',
        ])->assertSessionHasErrors('teacher_type_id');
    }

    public function test_teacher_import_template_can_be_downloaded(): void
    {
        $headmaster = $this->userWithRole(Roles::KEPALA_SEKOLAH);

        $this->actingAs($headmaster)
            ->get(route('teachers.template'))
            ->assertOk()
            ->assertDownload('template-import-guru.xlsx');
    }

    public function test_teacher_type_master_data_requires_kepala_sekolah(): void
    {
        [$guru] = $this->teacherUser(Roles::GURU);

        $this->actingAs($guru)->get(route('teacher-types.index'))->assertForbidden();

        $this->actingAs($this->userWithRole(Roles::KEPALA_SEKOLAH))
            ->get(route('teacher-types.index'))
            ->assertOk();
    }
}
