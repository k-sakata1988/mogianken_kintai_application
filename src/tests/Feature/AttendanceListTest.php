<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceListTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    /**
     * 自分の勤怠情報が全て表示される
     */
    public function test_only_own_attendances_are_displayed()
    {
        Attendance::factory()->create([
            'user_id' => $this->user->id,
            'date' => today(),
        ]);

        Attendance::factory()->create();

        $response = $this->get('/user/attendance/list');

        $response->assertStatus(200);
        $response->assertSee(today()->format('Y-m-d'));
    }

    /**
     * 勤怠一覧画面に現在の月が表示される
     */
    public function test_current_month_is_displayed()
    {
        $response = $this->get('/user/attendance/list');

        $response->assertStatus(200);
        $response->assertSee(now()->format('Y/m'));
    }

    /**
     * 前月の勤怠情報が表示される
     */
    public function test_previous_month_attendances_are_displayed()
    {
        $lastMonth = now()->subMonth();

        Attendance::factory()->create([
            'user_id' => $this->user->id,
            'date' => $lastMonth->copy()->startOfMonth(),
        ]);

        $response = $this->get('/user/attendance/list?month=' . $lastMonth->format('Y-m'));

        $response->assertStatus(200);
        $response->assertSee($lastMonth->format('Y/m'));
    }

    /**
     * 翌月の勤怠情報が表示される
     */
    public function test_next_month_attendances_are_displayed()
    {
        $nextMonth = now()->addMonth();

        Attendance::factory()->create([
            'user_id' => $this->user->id,
            'date' => $nextMonth->copy()->startOfMonth(),
        ]);

        $response = $this->get('/user/attendance/list?month=' . $nextMonth->format('Y-m'));

        $response->assertStatus(200);
        $response->assertSee($nextMonth->format('Y/m'));
    }

    /**
     * 詳細ボタンを押すと勤怠詳細画面に遷移する
     */
    public function test_detail_link_redirects_to_detail_page()
    {
        $attendance = Attendance::factory()->create([
            'user_id' => $this->user->id,
            'date' => today(),
        ]);

        $response = $this->get('/user/attendance/list');

        $response->assertStatus(200);
        $response->assertSee(
            route('user.attendance.show', $attendance)
        );
    }
}
