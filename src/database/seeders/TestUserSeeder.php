<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // テスト用ユーザー1
        User::updateOrCreate(
            ['email' => 'testuser1@example.com'],
            [
                'name'              => 'テストユーザー1',
                'password'          => Hash::make('password123'),
                'is_admin'          => false,
                'email_verified_at' => now(), // メール認証済み
            ]
        );

        // テスト用ユーザー2
        User::updateOrCreate(
            ['email' => 'testuser2@example.com'],
            [
                'name'              => 'テストユーザー2',
                'password'          => Hash::make('password123'),
                'is_admin'          => false,
                'email_verified_at' => now(),
            ]
        );
    }
}