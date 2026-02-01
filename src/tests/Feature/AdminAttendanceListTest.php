<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class AdminAttendanceListTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $users;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name' => '管理者ユーザー',
            'email' => 'admin@example.com',
            'password' => bcrypt('adminpass123'),
            'role' => 'admin',
        ]);

        $this->users = User::factory()->count(3)->create();
    }

    /**
     * 今日の勤怠一覧が正確に表示される
     */
    public function test_today_attendance_list_is_displayed()
    {
        $today = Carbon::today();

        foreach ($this->users as $user) {
            Attendance::factory()->create([
                'user_id' => $user->id,
                'date' => $today->toDateString(),
                'clock_in_time' => $today->copy()->addHours(9),
                'clock_out_time' => $today->copy()->addHours(18),
            ]);
        }

        $response = $this->actingAs($this->admin)
                         ->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee($today->format('Y年n月j日'));

        foreach ($this->users as $user) {
            $response->assertSee($user->name);
        }
    }

    /**
     * 「前日」ボタン押下で前日の勤怠情報が表示される
     */
    public function test_previous_day_attendance_list()
    {
        $yesterday = Carbon::yesterday();

        foreach ($this->users as $user) {
            Attendance::factory()->create([
                'user_id' => $user->id,
                'date' => $yesterday->toDateString(),
                'clock_in_time' => $yesterday->copy()->addHours(9),
                'clock_out_time' => $yesterday->copy()->addHours(18),
            ]);
        }

        $response = $this->actingAs($this->admin)
                         ->get(route('admin.dashboard', ['date' => $yesterday->toDateString()]));

        $response->assertStatus(200);
        $response->assertSee($yesterday->format('Y年n月j日'));

        foreach ($this->users as $user) {
            $response->assertSee($user->name);
        }
    }

    /**
     * 「翌日」ボタン押下で翌日の勤怠情報が表示される
     */
    public function test_next_day_attendance_list()
    {
        $tomorrow = Carbon::tomorrow();

        foreach ($this->users as $user) {
            Attendance::factory()->create([
                'user_id' => $user->id,
                'date' => $tomorrow->toDateString(),
                'clock_in_time' => $tomorrow->copy()->addHours(9),
                'clock_out_time' => $tomorrow->copy()->addHours(18),
            ]);
        }

        $response = $this->actingAs($this->admin)
                         ->get(route('admin.dashboard', ['date' => $tomorrow->toDateString()]));

        $response->assertStatus(200);
        $response->assertSee($tomorrow->format('Y年n月j日'));

        foreach ($this->users as $user) {
            $response->assertSee($user->name);
        }
    }
}
