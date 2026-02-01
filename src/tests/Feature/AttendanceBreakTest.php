<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\AttendanceBreak;

class AttendanceBreakTest extends TestCase
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
     * 休憩入ボタンが表示される & 休憩中にステータスが変わる
     */
    public function test_break_start_changes_status_to_breaking()
    {
        $user = User::first();

        $attendance = Attendance::factory()->working()->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($user);

        $response = $this->get('/user/attendance/index');
        $response->assertSee('休憩入');

        $response = $this->post('/user/attendance/break-start');
        $response->assertRedirect('/user/attendance/index');

        $attendance->refresh();
        $this->assertEquals('breaking', $attendance->status);

        $latestBreak = $attendance->breaks()->latest()->first();
        $this->assertNotNull($latestBreak->break_start);
        $this->assertNull($latestBreak->break_end);
    }

    /**
     * 休憩戻ボタンでステータスが出勤中に戻る & 休憩終了
     */
    public function test_break_end_changes_status_to_working()
    {
        $user = User::first();

        $attendance = Attendance::factory()->working()->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($user);

        $this->post('/user/attendance/break-start');

        $this->post('/user/attendance/break-end');

        $attendance->refresh();
        $this->assertEquals('working', $attendance->status);

        $latestBreak = $attendance->breaks()->latest()->first();
        $this->assertNotNull($latestBreak->break_start);
        $this->assertNotNull($latestBreak->break_end);
    }

    /**
     * 休憩は一日何回でも可能
     */
    public function test_multiple_breaks_allowed()
    {
        $user = User::first();

        $attendance = Attendance::factory()->working()->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($user);

        $this->post('/user/attendance/break-start');
        $this->post('/user/attendance/break-end');

        $this->post('/user/attendance/break-start');
        $attendance->refresh();

        $this->assertEquals('breaking', $attendance->status);
        $this->assertCount(2, $attendance->breaks); // breaks テーブルに2件記録
    }

    /**
     * 勤怠一覧で休憩時間が確認できる
     */
    public function test_break_times_displayed_in_list()
    {
        $user = User::first();

        $attendance = Attendance::factory()->working()->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($user);

        $this->post('/user/attendance/break-start');
        $this->post('/user/attendance/break-end');

        $response = $this->get('/user/attendance/list');
        $response->assertStatus(200);

        $attendance->refresh();
        $attendance->breaks->each(function ($break) use ($response) {
            $response->assertSee($break->break_start->format('H:i'));
            $response->assertSee($break->break_end->format('H:i'));
        });
    }
}
