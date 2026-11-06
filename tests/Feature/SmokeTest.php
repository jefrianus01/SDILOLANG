<?php

namespace Tests\Feature;

use App\Models\Classroom;
use App\Models\Gallery;
use App\Models\News;
use App\Models\Setting;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\Teacher;
use App\Models\TeacherAttendance;
use App\Models\User;
use App\Support\Roles;
use App\Support\SchoolSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\Concerns\CreatesSchoolData;
use Tests\TestCase;

class SmokeTest extends TestCase
{
    use CreatesSchoolData, RefreshDatabase;

    private User $superAdmin;

    private User $headmasterUser;

    private Teacher $headmasterTeacher;

    private User $waliUser;

    private Teacher $waliTeacher;

    private User $guruUser;

    private Teacher $guruTeacher;

    private Classroom $classroom;

    /** @var Collection<int, Student> */
    private Collection $students;

    private News $news;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedRoles();

        Setting::putMany([
            SchoolSetting::NAME => 'SDI LOLANG',
            SchoolSetting::ADDRESS => 'Jl. Pendidikan No. 1',
            SchoolSetting::LATITUDE => '1.4748000',
            SchoolSetting::LONGITUDE => '124.8421000',
            SchoolSetting::GEOFENCE_RADIUS => '50',
            SchoolSetting::LATE_AFTER => '07:15',
            SchoolSetting::CHECK_IN_TIME => '06:30',
            SchoolSetting::CHECK_OUT_TIME => '14:00',
        ], 'school');

        $year = $this->activeYear();

        [$this->headmasterUser, $this->headmasterTeacher] = $this->teacherUser(Roles::KEPALA_SEKOLAH);
        [$this->waliUser, $this->waliTeacher] = $this->teacherUser(Roles::WALI_KELAS);
        [$this->guruUser, $this->guruTeacher] = $this->teacherUser(Roles::GURU);
        $this->superAdmin = $this->userWithRole(Roles::SUPER_ADMIN);

        $this->classroom = $this->classroomFor($this->waliTeacher, $year);
        $this->students = collect([
            $this->studentIn($this->classroom, ['nama' => 'Siswa Satu']),
            $this->studentIn($this->classroom, ['nama' => 'Siswa Dua']),
        ]);

        TeacherAttendance::factory()->create([
            'teacher_id' => $this->waliTeacher->id,
            'date' => now()->toDateString(),
        ]);

        TeacherAttendance::factory()->create([
            'teacher_id' => $this->guruTeacher->id,
            'date' => now()->subDay()->toDateString(),
            'status' => 'izin',
            'approval_status' => 'pending',
            'reason' => 'Keperluan keluarga.',
        ]);

        foreach ($this->students as $student) {
            StudentAttendance::factory()->create([
                'student_id' => $student->id,
                'classroom_id' => $this->classroom->id,
                'date' => now()->toDateString(),
            ]);
        }

        $this->news = News::factory()->create(['title' => 'Berita Uji Coba']);
        Gallery::factory()->create();
    }

    public function test_public_pages_render(): void
    {
        $this->get('/')->assertOk();
        $this->get('/profil-sekolah')->assertOk();
        $this->get('/berita')->assertOk();
        $this->get('/berita/'.$this->news->slug)->assertOk();
        $this->get('/galeri')->assertOk();
    }

    public function test_super_admin_pages_render(): void
    {
        $this->actingAs($this->superAdmin);

        $urls = [
            '/dashboard',
            '/guru',
            '/guru/create',
            '/guru/import',
            '/master/jenis-guru',
            '/tahun-ajaran',
            '/kelas',
            '/siswa',
            '/siswa/import',
            '/siswa/promote',
            '/siswa/export/excel',
            '/siswa/export/pdf',
            '/absensi-siswa',
            '/absensi-siswa/kelas/'.$this->classroom->id,
            '/absensi-guru',
            '/absensi-guru/persetujuan',
            '/rekap/guru',
            '/rekap/guru/pdf',
            '/rekap/guru/excel',
            '/rekap/kelas/'.$this->classroom->id,
            '/rekap/kelas/'.$this->classroom->id.'/pdf',
            '/rekap/kelas/'.$this->classroom->id.'/excel',
            '/pengaturan',
            '/admin/berita',
            '/admin/berita/create',
            '/admin/galeri',
            '/admin/galeri/create',
            '/pengguna',
            '/log-aktivitas',
            '/profile',
        ];

        foreach ($urls as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_headmaster_pages_render(): void
    {
        $this->actingAs($this->headmasterUser);

        $urls = [
            '/dashboard',
            '/absensi-saya',
            '/guru',
            '/guru/create',
            '/guru/import',
            '/master/jenis-guru',
            '/tahun-ajaran',
            '/kelas',
            '/siswa',
            '/absensi-siswa',
            '/absensi-siswa/kelas/'.$this->classroom->id,
            '/absensi-guru',
            '/absensi-guru/persetujuan',
            '/rekap/guru',
            '/rekap/kelas/'.$this->classroom->id,
            '/pengaturan',
            '/admin/berita',
            '/admin/galeri',
            '/log-aktivitas',
            '/profile',
        ];

        foreach ($urls as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_wali_kelas_pages_render(): void
    {
        $this->actingAs($this->waliUser);

        $urls = [
            '/dashboard',
            '/absensi-saya',
            '/siswa',
            '/siswa/create',
            '/siswa/import',
            '/siswa/'.$this->students->first()->id.'/edit',
            '/siswa/export/excel',
            '/siswa/export/pdf',
            '/absensi-siswa',
            '/absensi-siswa/kelas/'.$this->classroom->id,
            '/rekap/kelas/'.$this->classroom->id,
            '/rekap/kelas/'.$this->classroom->id.'/pdf',
            '/rekap/kelas/'.$this->classroom->id.'/excel',
            '/profile',
        ];

        foreach ($urls as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_guru_pages_render(): void
    {
        $this->actingAs($this->guruUser);

        foreach (['/dashboard', '/absensi-saya', '/profile'] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_attendance_form_lists_students_for_homeroom(): void
    {
        $this->actingAs($this->waliUser);

        $this->get('/absensi-siswa/kelas/'.$this->classroom->id)
            ->assertOk()
            ->assertSee('Siswa Satu')
            ->assertSee('Simpan Absensi');
    }
}
