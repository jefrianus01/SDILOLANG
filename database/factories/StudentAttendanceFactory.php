<?php

namespace Database\Factories;

use App\Models\Classroom;
use App\Models\Student;
use App\Models\StudentAttendance;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StudentAttendance>
 */
class StudentAttendanceFactory extends Factory
{
    protected $model = StudentAttendance::class;

    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'classroom_id' => Classroom::factory(),
            'date' => now()->toDateString(),
            'status' => 'hadir',
            'recorded_by' => null,
        ];
    }
}
