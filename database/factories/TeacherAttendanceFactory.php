<?php

namespace Database\Factories;

use App\Models\Teacher;
use App\Models\TeacherAttendance;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TeacherAttendance>
 */
class TeacherAttendanceFactory extends Factory
{
    protected $model = TeacherAttendance::class;

    public function definition(): array
    {
        return [
            'teacher_id' => Teacher::factory(),
            'date' => now()->toDateString(),
            'check_in_at' => now()->setTime(6, 45),
            'check_in_lat' => 1.4748,
            'check_in_lng' => 124.8421,
            'check_in_distance' => 10,
            'check_out_at' => null,
            'status' => 'hadir',
            'approval_status' => 'not_required',
        ];
    }
}
