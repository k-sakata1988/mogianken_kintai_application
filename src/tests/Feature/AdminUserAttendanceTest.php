<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Attendance;

class AdminUserAttendanceTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $users;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'name' => '管理者',
            'email' => 'admin@example.com',
        ]);

        $this->users = User::factory()->count(3)->create([
            'role' => 'user'
        ]);
    }

    /** 管理者ユーザーが前一般ユーザーの「氏名」「メールアドレス」を確認できる */
    public function test_admin_can_see_all_users_in_staff_list()
    {
        $response = $this->actingAs($this->admin)
                         ->get(route('admin.staff.list'));

        $response->assertStatus(200);

        foreach ($this->users as $user) {
            $response->assertSee($user->name);
            $response->assertSee($user->email);
        }
    }

    /** ユーザーの勤怠情報が正しく表示される */
    public function test_admin_can_view_user_attendance_list()
    {
        $user = $this->users->first();

        // 勤怠データ作成
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'clock_in_time' => '09:00:00',
            'clock_out_time' => '18:00:00',
            'date' => now(),
        ]);

        $response = $this->actingAs($this->admin)
                         ->get(route('admin.staff.attendance', $user));

        $response->assertStatus(200)
                 ->assertSee('09:00')
                 ->assertSee('18:00');
    }

    /** 「前月」、「翌月」を押下した時に表示月の前月、翌月の情報が表示される */
    public function test_admin_can_view_previous_and_next_month_attendance()
    {
        $user = $this->users->first();

        $prevAttendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => now()->subMonth(),
            'clock_in_time' => '09:00:00',
            'clock_out_time' => '18:00:00',
        ]);

        $nextAttendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => now()->addMonth(),
            'clock_in_time' => '10:00:00',
            'clock_out_time' => '19:00:00',
        ]);

        $responsePrev = $this->actingAs($this->admin)
                             ->get(route('admin.staff.attendance', ['user' => $user, 'month' => now()->subMonth()->format('Y-m')]));
        $responsePrev->assertSee('09:00')->assertSee('18:00');

        $responseNext = $this->actingAs($this->admin)
                             ->get(route('admin.staff.attendance', ['user' => $user, 'month' => now()->addMonth()->format('Y-m')]));
        $responseNext->assertSee('10:00')->assertSee('19:00');
    }

    /** 「詳細」を押下するとその日の勤怠詳細画面に遷移する */
    public function test_admin_can_navigate_to_attendance_detail()
    {
        $user = $this->users->first();
        $attendance = Attendance::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($this->admin)
                         ->get(route('admin.staff.attendance', $user));

        $response->assertSee(route('admin.attendance.show', $attendance));
    }
}
