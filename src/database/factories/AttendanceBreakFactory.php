<?php

namespace Database\Factories;

use App\Models\Attendance;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceBreakFactory extends Factory
{
    public function definition()
    {
        return [
            'attendance_id' => Attendance::factory(),
            'break_start' => '12:00',
            'break_end' => '13:00',
        ];
    }
}
