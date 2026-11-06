<?php

namespace Database\Factories;

use App\Models\Classroom;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        return [
            'nomor_induk_siswa' => fake()->unique()->numerify('2026#####'),
            'nama' => fake()->name(),
            'classroom_id' => Classroom::factory(),
            'status' => 'aktif',
        ];
    }

    public function lulus(): static
    {
        return $this->state(fn () => ['status' => 'lulus']);
    }
}
