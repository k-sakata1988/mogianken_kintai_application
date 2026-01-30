<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceClockInTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        User::factory()->create([
            'name' => 'テストユーザー',
            'email' => 'user@example.com',
            'password' => bcrypt('password123'),
            'is_admin' => false,
        ]);
    }

    /**
     * 勤務外のユーザーが出勤できる
     */
    public function test_user_can_clock_in()
    {
        $user = User::first();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'status' => 'before_work',
        ]);

        $this->actingAs($user);

        $response = $this->get('/user/attendance/index');
        $response->assertStatus(200);
        $response->assertSee('出勤');

        $response = $this->post('/user/attendance/clock-in');
        $response->assertRedirect('/user/attendance/index');

        $attendance->refresh();
        $this->assertNotNull($attendance->clock_in_time);

        $response = $this->get('/user/attendance/index');
        $response->assertSee('出勤中');
    }

    /**
     * 退勤済のユーザーは出勤できない
     */
    public function test_user_cannot_clock_in_after_finished()
    {
        $user = User::first();

        $attendance = Attendance::factory()->finished()->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($user);

        $response = $this->get('/user/attendance/index');
        $response->assertStatus(200);
        $response->assertDontSee('出勤');
    }

    /**
     * 出勤時刻が勤怠一覧画面に表示される
     */
    public function test_clock_in_time_is_displayed_in_list()
    {
        $user = User::first();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'status' => 'before_work',
        ]);

        $this->actingAs($user);

        $this->post('/user/attendance/clock-in');

        $response = $this->get('/user/attendance/list');

        $attendance->refresh();
        $clockInTime = $attendance->clock_in_time->format('H:i');

        $response->assertStatus(200);
        $response->assertSee($clockInTime);
    }
}
