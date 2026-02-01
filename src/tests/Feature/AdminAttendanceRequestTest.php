<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use App\Models\Attendance;
use App\Models\AttendanceRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class AdminAttendanceRequestTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $user;
    protected $attendance;
    protected $request;

    protected function setUp(): void
    {
        parent::setUp();

        // 管理者ユーザー作成
        $this->admin = User::factory()->create([
            'role' => 'admin'
        ]);

        // 一般ユーザー作成
        $this->user = User::factory()->create([
            'role' => 'user'
        ]);

        // 勤怠作成
        $this->attendance = Attendance::factory()->create([
            'user_id' => $this->user->id,
            'date' => '2026-02-01',
            'clock_in_time' => '2026-02-01 09:00:00',
            'clock_out_time' => '2026-02-01 18:00:00',
            'is_modified' => false,
            'remark' => '初期備考',
        ]);

        // 修正申請（承認待ち）
        $this->request = AttendanceRequest::factory()->create([
            'attendance_id' => $this->attendance->id,
            'request_user_id' => $this->user->id,
            'reason' => '出勤時間修正',
            'after_data' => [
                'clock_in_time' => '09:00:00',
                'clock_out_time' => '18:00:00',
                'breaks' => [],
            ],
            'status' => 'pending',
        ]);

        // 修正申請（承認済み）
        AttendanceRequest::factory()->create([
            'attendance_id' => $this->attendance->id,
            'request_user_id' => $this->user->id,
            'reason' => '退勤時間修正',
            'after_data' => [
                'clock_in_time' => '09:00:00',
                'clock_out_time' => '18:30:00',
                'breaks' => [],
            ],
            'status' => 'approved',
        ]);
    }

    /** @test */
    public function pending_requests_are_displayed()
    {
        $response = $this->actingAs($this->admin)
                         ->get(route('admin.request.list', ['status' => 'pending']));

        $response->assertStatus(200)
                 ->assertSee('出勤時間修正')
                 ->assertDontSee('退勤時間修正')
                 ->assertSee($this->user->name);
    }

    /** @test */
    public function approved_requests_are_displayed()
    {
        $response = $this->actingAs($this->admin)
                         ->get(route('admin.request.list', ['status' => 'approved']));

        $response->assertStatus(200)
                 ->assertSee('退勤時間修正')
                 ->assertDontSee('出勤時間修正')
                 ->assertSee($this->user->name);
    }

    /** @test */
    public function request_detail_is_displayed()
    {
        $response = $this->actingAs($this->admin)
                         ->get(route('admin.request.show', $this->request));

        $response->assertStatus(200)
                 ->assertSee('出勤時間修正')
                 ->assertSee($this->user->name)
                 ->assertSee('09:00'); // clock_in_time の表示を Blade 形式に合わせて確認
    }

    /** @test */
    public function request_can_be_approved()
    {
        $response = $this->actingAs($this->admin)
                         ->patch(route('admin.request.approve', $this->request));

        $response->assertRedirect(route('admin.request.list'));
        $response->assertSessionHas('success', '申請を承認しました');

        $this->request->refresh();
        $this->attendance->refresh();

        // ステータス更新確認
        $this->assertEquals('approved', $this->request->status);

        // 勤怠情報更新確認
        $this->assertEquals(
            Carbon::parse($this->request->after_data['clock_in_time'])
                  ->format('H:i:s'),
            Carbon::parse($this->attendance->clock_in_time)->format('H:i:s')
        );

        $this->assertTrue((bool)$this->attendance->is_modified);
    }
}