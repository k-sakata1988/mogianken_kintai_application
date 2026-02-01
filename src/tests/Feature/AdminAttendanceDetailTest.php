<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\AttendanceBreak;
use App\Models\AttendanceRequest;

class AdminAttendanceDetailTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $user;
    protected $attendance;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name' => '管理者ユーザー',
            'email' => 'admin@example.com',
            'password' => bcrypt('adminpass123'),
            'role' => 'admin',
        ]);

        $this->user = User::factory()->create([
            'name' => '一般ユーザー',
            'role' => 'user',
        ]);

        $this->attendance = Attendance::factory()->create([
            'user_id' => $this->user->id,
            'clock_in_time' => '09:00:00',
            'clock_out_time' => '18:00:00',
            'remark' => '初期備考',
        ]);

        AttendanceBreak::factory()->create([
            'attendance_id' => $this->attendance->id,
            'break_start' => '12:00:00',
            'break_end'   => '13:00:00',
        ]);

        AttendanceRequest::factory()->create([
            'attendance_id' => $this->attendance->id,
            'request_user_id' => $this->admin->id,
            'reason' => '修正理由テスト',
        ]);
    }

    /** 勤怠詳細画面に正しいデータが表示されるか */
    public function test_attendance_detail_is_displayed()
    {
        $response = $this->actingAs($this->admin)
                         ->get(route('admin.attendance.show', $this->attendance));

        $response->assertStatus(200);
        $response->assertSee($this->user->name);
        $response->assertSee('09:00');
        $response->assertSee('18:00');

        $break = $this->attendance->breaks->first();
        $response->assertSee(substr($break->break_start, 11, 5));
        $response->assertSee(substr($break->break_end, 11, 5));

        $response->assertSee($this->attendance->remark ?? '');
    }

    /** 出勤時間が退勤時間より後の場合、バリデーションエラー */
    public function test_clock_in_after_clock_out_validation()
    {
        $data = [
            'clock_in_time'  => '19:00:00',
            'clock_out_time' => '18:00:00',
            'remark'         => 'テスト備考',
        ];

        $response = $this->actingAs($this->admin)
                         ->from(route('admin.attendance.show', $this->attendance))
                         ->patch(route('admin.attendance.update', $this->attendance), $data);

        $response->assertSessionHasErrors(['clock_in_time']);
    }

    /** 休憩開始時間が退勤時間より後の場合、バリデーションエラー */
    public function test_break_start_after_clock_out_validation()
    {
        $break = $this->attendance->breaks->first();

        $data = [
            'clock_in_time'  => '09:00:00',
            'clock_out_time' => '18:00:00',
            'break_start'    => '19:00:00',
            'break_end'      => $break->break_end,
            'remark'         => 'テスト備考',
        ];

        $response = $this->actingAs($this->admin)
                         ->from(route('admin.attendance.show', $this->attendance))
                         ->patch(route('admin.attendance.update', $this->attendance), $data);

        $response->assertSessionHasErrors(['break_start']);
    }

    /** 休憩終了時間が退勤時間より後の場合、バリデーションエラー */
    public function test_break_end_after_clock_out_validation()
    {
        $break = $this->attendance->breaks->first();

        $data = [
            'clock_in_time'  => '09:00:00',
            'clock_out_time' => '18:00:00',
            'break_start'    => $break->break_start,
            'break_end'      => '19:00:00',
            'remark'         => 'テスト備考',
        ];

        $response = $this->actingAs($this->admin)
                         ->from(route('admin.attendance.show', $this->attendance))
                         ->patch(route('admin.attendance.update', $this->attendance), $data);

        $response->assertSessionHasErrors(['break_end']);
    }

    /** 備考 が未入力の場合、バリデーションエラー */
    public function test_remark_required_validation()
    {
        $break = $this->attendance->breaks->first();

        $data = [
            'clock_in_time'  => '09:00:00',
            'clock_out_time' => '18:00:00',
            'break_start'    => $break->break_start,
            'break_end'      => $break->break_end,
            'remark'         => '',
        ];

        $response = $this->actingAs($this->admin)
                         ->from(route('admin.attendance.show', $this->attendance))
                         ->patch(route('admin.attendance.update', $this->attendance), $data);

        $response->assertSessionHasErrors(['remark']);
    }

    /** 休憩がない場合もバリデーションが通ることを確認 */
    public function test_validation_with_no_break()
    {
        $data = [
            'clock_in_time'  => '09:00:00',
            'clock_out_time' => '18:00:00',
            'remark'         => '備考あり',
            'break_start'    => null,
            'break_end'      => null,
        ];

        $response = $this->actingAs($this->admin)
                         ->from(route('admin.attendance.show', $this->attendance))
                         ->patch(route('admin.attendance.update', $this->attendance), $data);

        $response->assertSessionDoesntHaveErrors();
    }
}
