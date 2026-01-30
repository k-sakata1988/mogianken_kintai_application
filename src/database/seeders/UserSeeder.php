<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::updateOrCreate(
            ['email' => 'user1@example.com'],
            [
                'name'              => '一般ユーザー1',
                'password'          => Hash::make('password123'),
                'is_admin'          => false,
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'user2@example.com'],
            [
                'name'              => '一般ユーザー2',
                'password'          => Hash::make('password123'),
                'is_admin'          => false,
                'email_verified_at' => now(),
            ]
        );
    }
}