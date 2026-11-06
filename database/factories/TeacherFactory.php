<?php

namespace Database\Factories;

use App\Models\Teacher;
use App\Models\TeacherType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Teacher>
 */
class TeacherFactory extends Factory
{
    protected $model = Teacher::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'nama' => fake()->name(),
            'nip_nik' => fake()->unique()->numerify('################'),
            'teacher_type_id' => TeacherType::factory(),
            'status' => 'aktif',
        ];
    }

    public function nonaktif(): static
    {
        return $this->state(fn () => ['status' => 'nonaktif']);
    }
}
