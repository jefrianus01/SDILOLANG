<?php

namespace Database\Factories;

use App\Models\AcademicYear;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AcademicYear>
 */
class AcademicYearFactory extends Factory
{
    protected $model = AcademicYear::class;

    private static int $nextStartYear = 2020;

    public function definition(): array
    {
        $startYear = self::$nextStartYear++;

        return [
            'name' => $startYear.'/'.($startYear + 1),
            'starts_on' => $startYear.'-07-01',
            'ends_on' => ($startYear + 1).'-06-30',
            'is_active' => false,
        ];
    }

    public function active(): static
    {
        return $this->state(fn () => ['is_active' => true]);
    }
}
