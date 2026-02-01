<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\AttendanceBreak;
use Carbon\Carbon;

class AttendanceDetailTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 名前がログインユーザーの氏名になっている
     */
    public function test_user_name_is_displayed_on_detail_page()
    {
        $user = User::factory()->create([
            'name' => '山田 太郎',
        ]);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => today(),
        ]);

        $this->actingAs($user);
        $response = $this->get(
            route('user.attendance.show', $attendance)
        );

        $response->assertStatus(200);
        $response->assertSee('山田 太郎');
    }

    /**
     * 日付が選択した日付になっている
     */
    public function test_date_is_displayed_correctly()
    {
        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => Carbon::create(2026, 1, 15),
        ]);

        $this->actingAs($user);
        $response = $this->get(
            route('user.attendance.show', $attendance)
        );

        $response->assertStatus(200);
        $response->assertSee('2026年 1月15日');
    }

    /**
     * 出勤・退勤時刻が打刻と一致している
     */
    public function test_clock_in_and_out_time_are_displayed_correctly()
    {
        $user = User::factory()->create();

        $clockIn  = Carbon::create(2026, 1, 15, 9, 0);
        $clockOut = Carbon::create(2026, 1, 15, 18, 0);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => $clockIn->toDateString(),
            'clock_in_time' => $clockIn,
            'clock_out_time' => $clockOut,
        ]);

        $this->actingAs($user);
        $response = $this->get(
            route('user.attendance.show', $attendance)
        );

        $response->assertStatus(200);
        $response->assertSee('09:00');
        $response->assertSee('18:00');
    }

    /**
     * 休憩時刻が打刻と一致している
     */
    public function test_break_time_is_displayed_correctly()
    {
        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => today(),
        ]);

        $breakStart = Carbon::create(2026, 1, 15, 12, 0);
        $breakEnd   = Carbon::create(2026, 1, 15, 13, 0);

        AttendanceBreak::factory()->create([
            'attendance_id' => $attendance->id,
            'break_start' => $breakStart,
            'break_end' => $breakEnd,
        ]);

        $this->actingAs($user);
        $response = $this->get(
            route('user.attendance.show', $attendance)
        );

        $response->assertStatus(200);
        $response->assertSee('12:00');
        $response->assertSee('13:00');
    }
}
