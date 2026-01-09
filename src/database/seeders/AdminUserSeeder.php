<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name'              => 'admin',
                'password'          => Hash::make('password123'),
                'is_admin'          => true,
                'email_verified_at' => now(), // 管理者はメール認証不要
            ]
        );
    }
}
