<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Carbon\Carbon;

class AttendanceDateTimeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        User::factory()->create([
            'name' => '一般ユーザー',
            'email' => 'user@example.com',
            'password' => bcrypt('password123'),
            'is_admin' => false,
        ]);

        setlocale(LC_TIME, 'ja_JP.UTF-8');
    }

    /**
     * 勤怠画面に現在日時が表示されているか確認
     */
    public function test_current_datetime_is_displayed()
    {
        $user = User::first();

        $this->actingAs($user);

        $response = $this->get('/user/attendance/index');

        $response->assertStatus(200);

        $now = Carbon::now();

        $expectedDate = $now->formatLocalized('%Y年%-m月%-d日(%a)');
        $expectedTime = $now->format('H:i');

        $response->assertSee($expectedDate);
        $response->assertSee($expectedTime);
    }
}
