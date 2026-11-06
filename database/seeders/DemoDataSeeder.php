<?php

namespace Database\Seeders;

use App\Enums\ApprovalStatus;
use App\Enums\AttendanceStatus;
use App\Enums\TeacherAttendanceStatus;
use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Gallery;
use App\Models\News;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\Teacher;
use App\Models\TeacherAttendance;
use App\Models\TeacherType;
use App\Models\User;
use App\Support\Roles;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $year = AcademicYear::updateOrCreate(
            ['name' => '2026/2027'],
            ['starts_on' => '2026-07-13', 'ends_on' => '2027-06-30', 'is_active' => true]
        );

        AcademicYear::query()->where('id', '!=', $year->id)->update(['is_active' => false]);

        $classrooms = [];
        foreach (range(1, 6) as $level) {
            $classrooms[$level] = Classroom::updateOrCreate(
                ['academic_year_id' => $year->id, 'level' => $level],
                ['name' => 'Kelas '.$level]
            );
        }

        $types = TeacherType::query()->pluck('id', 'name');

        $superAdmin = User::updateOrCreate(
            ['username' => 'superadmin'],
            [
                'name' => 'Super Administrator',
                'email' => 'superadmin@example.com',
                'password' => 'Super#2026',
                'must_change_password' => false,
                'is_active' => true,
            ]
        );
        $superAdmin->syncRoles([Roles::SUPER_ADMIN]);

        $teachersData = [
            ['nama' => 'Kepala Sekolah', 'nip' => '197001012000011001', 'type' => 'Kepala Sekolah'],
            ['nama' => 'Guru Kelas 1', 'nip' => '198502032010011002', 'type' => 'Guru Kelas'],
            ['nama' => 'Guru Kelas 2', 'nip' => '198703042011011003', 'type' => 'Guru Kelas'],
            ['nama' => 'Guru Kelas 3', 'nip' => '198904052012012004', 'type' => 'Guru Kelas'],
            ['nama' => 'Guru Kelas 4', 'nip' => '199005062013012005', 'type' => 'Guru Kelas'],
            ['nama' => 'Guru Kelas 5', 'nip' => '199106072014011006', 'type' => 'Guru Kelas'],
            ['nama' => 'Guru Kelas 6', 'nip' => '199207082015012007', 'type' => 'Guru Kelas'],
            ['nama' => 'Guru PAI', 'nip' => '199308092016011008', 'type' => 'Guru PAI'],
            ['nama' => 'Guru PJOK', 'nip' => '199409102017011009', 'type' => 'Guru PJOK'],
            ['nama' => 'Operator', 'nip' => '199510112018012010', 'type' => 'Operator'],
        ];

        $teachers = [];

        foreach ($teachersData as $data) {
            $user = User::updateOrCreate(
                ['username' => $data['nip']],
                [
                    'name' => $data['nama'],
                    'password' => 'Guru#2026',
                    'must_change_password' => false,
                    'is_active' => true,
                ]
            );

            $teacher = Teacher::updateOrCreate(
                ['nip_nik' => $data['nip']],
                [
                    'user_id' => $user->id,
                    'nama' => $data['nama'],
                    'teacher_type_id' => $types[$data['type']],
                    'status' => 'aktif',
                ]
            );

            $user->syncRoles([Roles::GURU]);
            $teachers[$data['nip']] = $teacher;
        }

        $headmaster = $teachers['197001012000011001'];
        $headmaster->user->syncRoles([Roles::GURU, Roles::KEPALA_SEKOLAH]);

        // Wali kelas 1-6
        $homeroomNips = [
            1 => '198502032010011002',
            2 => '198703042011011003',
            3 => '198904052012012004',
            4 => '199005062013012005',
            5 => '199106072014011006',
            6 => '199207082015012007',
        ];

        foreach ($homeroomNips as $level => $nip) {
            $classrooms[$level]->update(['wali_kelas_id' => $teachers[$nip]->id]);
            $teachers[$nip]->user->syncRoles([Roles::GURU, Roles::WALI_KELAS]);
        }

        $this->seedStudents($classrooms);
        $this->seedAttendances($teachers, $headmaster);
        $this->seedSiteContent($headmaster);
    }

    /**
     * @param  array<int, Classroom>  $classrooms
     */
    private function seedStudents(array $classrooms): void
    {
        $index = 0;

        foreach ($classrooms as $level => $classroom) {
            for ($number = 1; $number <= 10; $number++) {
                $nis = '2026'.$level.str_pad((string) $number, 3, '0', STR_PAD_LEFT);

                Student::updateOrCreate(
                    ['nomor_induk_siswa' => $nis],
                    [
                        'nama' => sprintf('Siswa %02d', $index + 1),
                        'classroom_id' => $classroom->id,
                        'status' => 'aktif',
                    ]
                );

                $index++;
            }
        }
    }

    /**
     * @param  array<string, Teacher>  $teachers
     */
    private function seedAttendances(array $teachers, Teacher $headmaster): void
    {
        $workdays = collect(range(0, 13))
            ->map(fn (int $daysAgo) => now()->subDays($daysAgo)->startOfDay())
            ->filter(fn ($date) => $date->isWeekday())
            ->take(10)
            ->values();

        foreach (array_values($teachers) as $teacherIndex => $teacher) {
            foreach ($workdays as $dayIndex => $date) {
                if ($teacher->id === $teachers['199308092016011008']->id && $dayIndex === 0) {
                    continue;
                }

                $checkIn = $date->copy()->setTime(6, 40 + (($teacherIndex * 3) % 30));
                $late = $teacherIndex % 4 === 0 && $dayIndex % 3 === 0;

                if ($late) {
                    $checkIn->setTime(7, 20 + ($teacherIndex % 20));
                }

                TeacherAttendance::updateOrCreate(
                    ['teacher_id' => $teacher->id, 'date' => $date->toDateString()],
                    [
                        'check_in_at' => $checkIn,
                        'check_in_lat' => 1.4748000,
                        'check_in_lng' => 124.8421000,
                        'check_in_distance' => 5 + ($teacherIndex * 2) % 30,
                        'check_out_at' => $date->copy()->setTime(14, 0 + ($teacherIndex % 10)),
                        'check_out_lat' => 1.4748000,
                        'check_out_lng' => 124.8421000,
                        'check_out_distance' => 6 + ($teacherIndex * 2) % 30,
                        'status' => $late ? TeacherAttendanceStatus::Terlambat->value : TeacherAttendanceStatus::Hadir->value,
                        'approval_status' => ApprovalStatus::NotRequired->value,
                    ]
                );
            }
        }

        TeacherAttendance::updateOrCreate(
            ['teacher_id' => $teachers['199308092016011008']->id, 'date' => now()->toDateString()],
            [
                'check_in_at' => null,
                'check_in_lat' => null,
                'check_in_lng' => null,
                'check_in_distance' => null,
                'check_out_at' => null,
                'status' => TeacherAttendanceStatus::Izin->value,
                'approval_status' => ApprovalStatus::Pending->value,
                'reason' => 'Menghadiri acara keluarga di luar kota.',
            ]
        );

        TeacherAttendance::updateOrCreate(
            ['teacher_id' => $teachers['199409102017011009']->id, 'date' => now()->subDay()->toDateString()],
            [
                'check_in_at' => null,
                'status' => TeacherAttendanceStatus::DinasLuar->value,
                'approval_status' => ApprovalStatus::Approved->value,
                'reason' => 'Pendampingan siswa lomba olahraga tingkat kecamatan.',
                'approved_by' => $headmaster->user_id,
                'approved_at' => now(),
            ]
        );

        $studentDays = $workdays->take(5);
        $weights = [
            AttendanceStatus::Hadir->value => 88,
            AttendanceStatus::Sakit->value => 4,
            AttendanceStatus::Izin->value => 5,
            AttendanceStatus::Alpa->value => 3,
        ];

        foreach (Classroom::query()->get() as $classroom) {
            $students = Student::query()->where('classroom_id', $classroom->id)->get();

            foreach ($studentDays as $date) {
                foreach ($students as $student) {
                    StudentAttendance::updateOrCreate(
                        ['student_id' => $student->id, 'date' => $date->toDateString()],
                        [
                            'classroom_id' => $classroom->id,
                            'status' => $this->weightedStatus($weights),
                            'recorded_by' => $classroom->waliKelas?->user?->id,
                        ]
                    );
                }
            }
        }
    }

    /**
     * @param  array<string, int>  $weights
     */
    private function weightedStatus(array $weights): string
    {
        $roll = random_int(1, array_sum($weights));

        foreach ($weights as $status => $weight) {
            $roll -= $weight;

            if ($roll <= 0) {
                return $status;
            }
        }

        return AttendanceStatus::Hadir->value;
    }

    private function seedSiteContent(Teacher $headmaster): void
    {
        $articles = [
            [
                'title' => 'Peringatan Hari Guru Nasional 2026',
                'excerpt' => 'Seluruh warga sekolah mengikuti upacara dan pentas seni dalam rangka Hari Guru Nasional.',
                'body' => '<p>Sekolah menggelar upacara bendera dan pentas seni dalam rangka memperingati Hari Guru Nasional. Kegiatan diikuti oleh seluruh siswa, guru, dan tenaga kependidikan.</p><p>Kepala sekolah menyampaikan apresiasi kepada para guru yang telah mendidik dengan penuh dedikasi. Acara ditutup dengan pemberian penghargaan kepada guru berprestasi.</p>',
                'days_ago' => 2,
            ],
            [
                'title' => 'Lomba Kebersihan Antar Kelas',
                'excerpt' => 'Program sekolah sehat mengadakan penilaian kebersihan kelas setiap pekan.',
                'body' => '<p>Dalam rangka menumbuhkan budaya peduli lingkungan, sekolah mengadakan lomba kebersihan antar kelas. Penilaian meliputi kebersihan ruang kelas, pengelolaan sampah, dan penghijauan.</p><p>Kelas 4 berhasil meraih juara pertama pada penilaian bulan ini.</p>',
                'days_ago' => 9,
            ],
            [
                'title' => 'Penerimaan Rapor Tengah Semester',
                'excerpt' => 'Orang tua siswa diundang hadir untuk menerima laporan hasil belajar tengah semester.',
                'body' => '<p>Penerimaan rapor tengah semester dilaksanakan pada hari Sabtu. Orang tua siswa hadir untuk berdiskusi dengan wali kelas mengenai perkembangan belajar anak.</p><p>Sekolah mengingatkan pentingnya pendampingan belajar di rumah.</p>',
                'days_ago' => 16,
            ],
        ];

        foreach ($articles as $article) {
            News::updateOrCreate(
                ['slug' => Str::slug($article['title'])],
                [
                    'title' => $article['title'],
                    'excerpt' => $article['excerpt'],
                    'body' => $article['body'],
                    'is_published' => true,
                    'published_at' => now()->subDays($article['days_ago']),
                    'author_id' => $headmaster->user_id,
                ]
            );
        }

        $placeholder = public_path('images/gallery-placeholder.svg');

        if (File::exists($placeholder)) {
            $captions = [
                ['title' => 'Kegiatan Belajar di Kelas', 'caption' => 'Suasana pembelajaran aktif di kelas 3.'],
                ['title' => 'Senam Pagi Bersama', 'caption' => 'Senam pagi setiap Jumat untuk menjaga kebugaran siswa.'],
                ['title' => 'Perpustakaan Sekolah', 'caption' => 'Siswa membaca buku di perpustakaan sekolah.'],
            ];

            foreach ($captions as $index => $item) {
                $path = 'gallery/demo-'.($index + 1).'.svg';
                Storage::disk('public')->put($path, File::get($placeholder));

                Gallery::updateOrCreate(
                    ['title' => $item['title']],
                    [
                        'caption' => $item['caption'],
                        'image_path' => $path,
                        'sort_order' => $index + 1,
                        'is_published' => true,
                    ]
                );
            }
        }
    }
}
