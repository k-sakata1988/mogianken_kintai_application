<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\AttendanceRequest;

class AttendanceDetailUpdateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 出勤時間が退勤時間より後の場合エラー
     */
    public function test_clock_in_after_clock_out_shows_error()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $response = $this->post(route('user.attendance.request.store', $attendance), [
            'clock_in_time'  => '18:00',
            'clock_out_time' => '09:00',
            'reason' => 'テスト理由',
        ]);

        $response->assertSessionHasErrors([
            'clock_in_time' => '出勤時間、もしくは退勤時間が不適切な値です',
        ]);

        $this->assertDatabaseCount('attendance_requests', 0);
    }

    /**
     * 休憩開始が退勤時間より後の場合エラー
     */
    public function test_break_start_after_clock_out_shows_error()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $response = $this->post(route('user.attendance.request.store', $attendance), [
            'clock_in_time'  => '09:00',
            'clock_out_time' => '18:00',
            'breaks' => [
                ['start' => '19:00', 'end' => '19:30'],
            ],
            'reason' => 'テスト理由',
        ]);

        $response->assertSessionHasErrors([
            'breaks' => '休憩時間、もしくは退勤時間が不適切な値です',
        ]);
    }

    /**
     * 休憩終了が退勤時間より後の場合エラー
     */
    public function test_break_end_after_clock_out_shows_error()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $response = $this->post(route('user.attendance.request.store', $attendance), [
            'clock_in_time'  => '09:00',
            'clock_out_time' => '18:00',
            'breaks' => [
                ['start' => '17:00', 'end' => '19:00'],
            ],
            'reason' => 'テスト理由',
        ]);

        $response->assertSessionHasErrors([
            'breaks' => '休憩時間、もしくは退勤時間が不適切な値です',
        ]);
    }

    /**
     * 備考未入力はエラー
     */
    public function test_reason_is_required()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $response = $this->post(route('user.attendance.request.store', $attendance), [
            'clock_in_time'  => '09:00',
            'clock_out_time' => '18:00',
            'reason' => '',
        ]);

        $response->assertSessionHasErrors([
            'reason' => '備考を記入してください',
        ]);
    }

    /**
     * 修正申請が作成される
     */
    public function test_attendance_correction_request_is_created()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $this->post(route('user.attendance.request.store', $attendance), [
            'clock_in_time'  => '09:00',
            'clock_out_time' => '18:00',
            'reason' => '修正申請テスト',
        ]);

        $this->assertDatabaseHas('attendance_requests', [
            'attendance_id'   => $attendance->id,
            'request_user_id' => $user->id,
            'status' => 'pending',
        ]);
    }

    /**
     * 承認前は attendance が更新されない
     */
    public function test_attendance_is_not_updated_while_pending()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'clock_in_time' => '09:00',
        ]);

        AttendanceRequest::factory()->create([
            'attendance_id' => $attendance->id,
            'request_user_id' => $user->id,
            'status' => 'pending',
            'after_data' => ['clock_in_time' => '10:00'],
        ]);

        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'clock_in_time' => $attendance->clock_in_time,
        ]);
    }

    /**
     * 管理者承認後に attendance が更新される
     */
    public function test_attendance_is_updated_after_approval()
    {
        $user  = User::factory()->create();
        $admin = User::factory()->create(['role' => 'admin']);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'clock_in_time' => '09:00',
        ]);

        $request = AttendanceRequest::factory()->create([
            'attendance_id' => $attendance->id,
            'request_user_id' => $user->id,
            'status' => 'pending',
            'after_data' => ['clock_in_time' => '10:00'],
        ]);

        $this->actingAs($admin);

        $this->patch(route('admin.request.approve', ['attendanceRequest' => $request->id]));

        $attendance->refresh();

        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'clock_in_time' => $attendance->date->format('Y-m-d') . ' 10:00:00',
        ]);
    }
}