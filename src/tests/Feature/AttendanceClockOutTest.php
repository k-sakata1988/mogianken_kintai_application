<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Attendance;

class AttendanceClockOutTest extends TestCase
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
     * 退勤ボタンが表示され、退勤処理でステータスが退勤済になる
     */
    public function test_clock_out_changes_status_to_finished()
    {
        $user = User::first();

        $attendance = Attendance::factory()->working()->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($user);

        $response = $this->get('/user/attendance/index');
        $response->assertStatus(200);
        $response->assertSee('退勤');

        $response = $this->post('/user/attendance/clock-out');
        $response->assertRedirect('/user/attendance/index');

        $attendance->refresh();

        $this->assertEquals('finished', $attendance->status);
        $this->assertNotNull($attendance->clock_out_time);
    }

    /**
     * 勤怠一覧画面で退勤時刻が表示される
     */
    public function test_clock_out_time_is_displayed_in_list()
    {
        $user = User::first();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'status' => 'before_work',
        ]);

        $this->actingAs($user);

        $this->post('/user/attendance/clock-in');

        $this->post('/user/attendance/clock-out');

        $attendance->refresh();

        $response = $this->get('/user/attendance/list');
        $response->assertStatus(200);

        $response->assertSee(
            $attendance->clock_out_time->format('H:i')
        );
    }
}
