<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class AdminLoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // 管理者ユーザーを作成
        User::factory()->create([
            'name' => '管理者ユーザー',
            'email' => 'admin@example.com',
            'password' => bcrypt('adminpass123'),
            'role' => 'admin',
        ]);
    }

    /**
     * メールアドレスが未入力の場合
     */
    public function test_email_is_required_for_admin_login()
    {
        $formData = [
            'email' => '',
            'password' => 'adminpass123',
            'login_type' => 'admin', // 管理者ログイン用フラグ
        ];

        $response = $this->post('/login', $formData);

        $response->assertSessionHasErrors(['email'=> 'メールアドレスを入力してください',]);
    }

    /**
     * パスワードが未入力の場合
     */
    public function test_password_is_required_for_admin_login()
    {
        $formData = [
            'email' => 'admin@example.com',
            'password' => '',
            'login_type' => 'admin',
        ];

        $response = $this->post('/login', $formData);

        $response->assertSessionHasErrors(['password'=> 'パスワードを入力してください',]);
    }

    /**
     * 登録内容と一致しない場合
     */
    public function test_invalid_credentials_for_admin()
    {
        $formData = [
            'email' => 'wrong@example.com',
            'password' => 'adminpass123',
            'login_type' => 'admin',
        ];

        $response = $this->post('/login', $formData);

        $response->assertSessionHasErrors(['email'=> 'ログイン情報が登録されていません',]);
    }
}
