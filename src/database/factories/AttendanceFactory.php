<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Attendance;
use App\Models\User;

class AttendanceFactory extends Factory
{
    protected $model = Attendance::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'date' => now(),
            'status' => 'before_work',
            'clock_in_time' => null,
            'clock_out_time' => null,
            'total_working_time' => 0,
            'total_break_time' => 0,
        ];
    }
    public function working()
    {
        return $this->state(fn () => [
            'status' => 'working',
            'clock_in_time' => now(),
        ]);
    }

    public function breaking()
    {
        return $this->state(fn () => [
            'status' => 'breaking',
            'clock_in_time' => now(),
            'total_break_time' => 15, // 任意
        ]);
    }

    public function finished()
    {
        return $this->state(fn () => [
            'status' => 'finished',
            'clock_in_time' => now()->subHours(8),
            'clock_out_time' => now(),
            'total_working_time' => 480,
        ]);
    }
}
