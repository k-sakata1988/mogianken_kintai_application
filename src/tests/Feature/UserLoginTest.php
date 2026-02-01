<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class UserLoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // テスト用ユーザーを作成
        User::factory()->create([
            'name' => 'テストユーザー',
            'email' => 'testuser@example.com',
            'password' => bcrypt('password123'),
            'role' => 'user'
        ]);
    }

    /**
     * メールアドレスが未入力の場合
     */
    public function test_email_is_required_for_login()
    {
        $formData = [
            'email' => '',
            'password' => 'password123',
        ];

        $response = $this->post('/login', $formData);

        $response->assertSessionHasErrors([
            'email' => 'メールアドレスを入力してください',
        ]);
    }

    /**
     * パスワードが未入力の場合
     */
    public function test_password_is_required_for_login()
    {
        $formData = [
            'email' => 'testuser@example.com',
            'password' => '',
        ];

        $response = $this->post('/login', $formData);

        $response->assertSessionHasErrors([
            'password' => 'パスワードを入力してください',
        ]);
    }

    /**
     * 登録内容と一致しない場合
     */
    public function test_invalid_credentials()
    {
        $formData = [
            'email' => 'wrong@example.com',
            'password' => 'password123',
        ];

        $response = $this->post('/login', $formData);

        $response->assertSessionHasErrors([
            'email' => 'ログイン情報が登録されていません',
        ]);
    }
}
