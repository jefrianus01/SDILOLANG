<?php

namespace Database\Seeders;

use App\Models\TeacherType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TeacherTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Kepala Sekolah', 'description' => 'Pimpinan sekolah'],
            ['name' => 'Guru Kelas', 'description' => 'Guru kelas 1-6'],
            ['name' => 'Guru PAI', 'description' => 'Pendidikan Agama Islam'],
            ['name' => 'Guru PJOK', 'description' => 'Pendidikan Jasmani, Olahraga, dan Kesehatan'],
            ['name' => 'Guru Bahasa Inggris', 'description' => 'Bahasa Inggris'],
            ['name' => 'Guru Mulok', 'description' => 'Muatan lokal'],
            ['name' => 'Guru Bahasa Indonesia', 'description' => 'Bahasa Indonesia'],
            ['name' => 'Guru Matematika', 'description' => 'Matematika'],
            ['name' => 'Guru IPA', 'description' => 'Ilmu Pengetahuan Alam'],
            ['name' => 'Operator', 'description' => 'Operator sekolah / administrasi'],
        ];

        foreach ($types as $type) {
            TeacherType::updateOrCreate(
                ['name' => $type['name']],
                [
                    'slug' => Str::slug($type['name']),
                    'description' => $type['description'],
                    'is_active' => true,
                ]
            );
        }
    }
}
