<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Attendance;

class AttendanceStatusTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        User::factory()->create([
            'name' => 'テストユーザー',
            'email' => 'user@example.com',
            'password' => bcrypt('password123'),
            'role' => 'user',
        ]);
    }

    /**
     * 勤務外ステータスが表示される
     */
    public function test_before_work_status_is_displayed()
    {
        $user = User::first();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'status' => 'before_work',
        ]);

        $this->actingAs($user);
        $response = $this->get('/user/attendance/index');

        $response->assertStatus(200);
        $response->assertSee('勤務外');
    }

    /**
     * 出勤中ステータスが表示される
     */
    public function test_working_status_is_displayed()
    {
        $user = User::first();

        Attendance::factory()->working()->create([
            'user_id' => $user->id,
            'date' => today(),
        ]);

        $this->actingAs($user);
        $response = $this->get('/user/attendance/index');

        $response->assertStatus(200);
        $response->assertSee('出勤中');
    }

    /**
     * 休憩中ステータスが表示される
     */
    public function test_breaking_status_is_displayed()
    {
        $user = User::first();

        $attendance = Attendance::factory()->working()->create([
            'user_id' => $user->id,
            'date' => today(),
        ]);

        $attendance->breaks()->create([
            'break_start' => now(),
            'break_end' => null,
        ]);

        $this->actingAs($user);
        $response = $this->get('/user/attendance/index');

        $response->assertStatus(200);
        $response->assertSee('休憩中');
    }

    /**
     * 退勤済ステータスが表示される
     */
    public function test_finished_status_is_displayed()
    {
        $user = User::first();

        Attendance::factory()->finished()->create([
            'user_id' => $user->id,
            'date' => today(),
        ]);

        $this->actingAs($user);
        $response = $this->get('/user/attendance/index');

        $response->assertStatus(200);
        $response->assertSee('退勤済');
    }
}
