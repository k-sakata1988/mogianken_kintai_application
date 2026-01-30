<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class UserRegistrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 名前が未入力の場合、バリデーションエラーが返る
     */
    public function test_name_is_required()
    {
        $formData = [
            'name' => '',
            'email' => 'testuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->post('/register', $formData);

        $response->assertSessionHasErrors([
            'name' => 'お名前を入力してください。',
        ]);

        $this->assertDatabaseMissing('users', [
            'email' => 'testuser@example.com',
        ]);
    }

    /**
     * メールアドレスが未入力の場合、バリデーションエラーが返る
     */
    public function test_email_is_required()
    {
        $formData = [
            'name' => 'テストユーザー',
            'email' => '',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->post('/register', $formData);

        $response->assertSessionHasErrors([
            'email' => 'メールアドレスを入力してください。',
        ]);

        $this->assertDatabaseMissing('users', [
            'name' => 'テストユーザー',
        ]);
    }

    /**
     * パスワードが8文字未満の場合、バリデーションエラーが返る
     */
    public function test_password_min_length()
    {
        $formData = [
            'name' => 'テストユーザー',
            'email' => 'testuser2@example.com',
            'password' => 'pass12', // 8文字未満
            'password_confirmation' => 'pass12',
        ];

        $response = $this->post('/register', $formData);

        $response->assertSessionHasErrors([
            'password' => 'パスワードは8文字以上で入力してください。',
        ]);

        $this->assertDatabaseMissing('users', [
            'email' => 'testuser2@example.com',
        ]);
    }

    /**
     * パスワードが確認用と一致しない場合、バリデーションエラーが返る
     */
    public function test_password_confirmation_mismatch()
    {
        $formData = [
            'name' => 'テストユーザー',
            'email' => 'testuser3@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password321', // 不一致
        ];

        $response = $this->post('/register', $formData);

        $response->assertSessionHasErrors([
            'password' => 'パスワードと一致しません。',
        ]);

        $this->assertDatabaseMissing('users', [
            'email' => 'testuser3@example.com',
        ]);
    }

    /**
     * パスワードが未入力の場合、バリデーションエラーが返る
     */
    public function test_password_is_required()
    {
        $formData = [
            'name' => 'テストユーザー',
            'email' => 'testuser4@example.com',
            'password' => '',
            'password_confirmation' => '',
        ];

        $response = $this->post('/register', $formData);

        $response->assertSessionHasErrors([
            'password' => 'パスワードを入力してください。',
        ]);

        $this->assertDatabaseMissing('users', [
            'email' => 'testuser4@example.com',
        ]);
    }

    /**
     * 正常なユーザー情報を入力した場合、データベースに登録される
     */
    public function test_successful_registration()
    {
        $formData = [
            'name' => '正常ユーザー',
            'email' => 'validuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->post('/register', $formData);

        $response->assertRedirect('/user/attendance/index');

        $this->assertDatabaseHas('users', [
            'email' => 'validuser@example.com',
            'name' => '正常ユーザー',
        ]);
    }
}
