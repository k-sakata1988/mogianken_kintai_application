<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Attendance;
use App\Models\User;
use App\Models\AttendanceRequest;

class AttendanceRequestFactory extends Factory
{
    protected $model = AttendanceRequest::class;

    public function definition()
    {
        return [
            'attendance_id'     => Attendance::factory(),
            'request_user_id'   => User::factory(),
            'approver_user_id'  => null,
            'status'            => 'pending',
            'reason'            => '修正理由テスト',
            'before_data'       => json_encode([]),
            'after_data'        => json_encode($this->faker->optional()->randomElement([
            ['clock_in_time' => '10:00'],
            ['clock_out_time' => '18:00'],
            ['clock_in_time' => '10:00', 'clock_out_time' => '18:00'],
        ])),
    ];
}

    public function approved()
    {
        return $this->state(function () {
            return [
                'status' => 'approved',
                'approver_user_id' => User::factory(),
            ];
        });
    }

    public function rejected()
    {
        return $this->state(function () {
            return [
                'status' => 'rejected',
                'approver_user_id' => User::factory(),
            ];
        });
    }
}
