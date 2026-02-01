<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    /** 会員登録後、認証メールが送信される */
    public function test_registration_sends_verification_email()
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'test@example.com',
            'email_verified_at' => null,
        ]);

        $user->sendEmailVerificationNotification();

        Notification::assertSentTo($user, VerifyEmail::class);
    }

    /** メール認証誘導画面で「認証はこちらから」ボタンを押下するとメール認証サイトに遷移する */
    public function test_email_verification_link_redirects_to_verification_site()
    {
        Notification::fake();

        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $user->sendEmailVerificationNotification();

        Notification::assertSentTo($user, VerifyEmail::class, function ($notification, $channels) use ($user) {

            $mailData = $notification->toMail($user);

            $verificationUrl = $mailData->actionUrl;

            $response = $this->actingAs($user)->get($verificationUrl);

            $response->assertRedirect('/user/attendance/index');
            return true;
        });
    }

    /** メール認証サイトのメール認証を完了すると、勤怠登録画面に遷移する */
    public function test_verified_user_can_access_attendance_page()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)->get('/user/attendance/index');

        $response->assertStatus(200);
    }
}
