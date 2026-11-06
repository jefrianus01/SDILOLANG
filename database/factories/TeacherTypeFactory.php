<?php

namespace Database\Factories;

use App\Models\TeacherType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<TeacherType>
 */
class TeacherTypeFactory extends Factory
{
    protected $model = TeacherType::class;

    public function definition(): array
    {
        $name = 'Guru '.fake()->unique()->word();

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => null,
            'is_active' => true,
        ];
    }
}
