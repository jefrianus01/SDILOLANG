<?php

namespace Database\Factories;

use App\Models\AcademicYear;
use App\Models\Classroom;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Classroom>
 */
class ClassroomFactory extends Factory
{
    protected $model = Classroom::class;

    public function definition(): array
    {
        $level = fake()->numberBetween(1, 6);

        return [
            'academic_year_id' => AcademicYear::factory(),
            'name' => 'Kelas '.$level,
            'level' => $level,
            'wali_kelas_id' => null,
        ];
    }
}
