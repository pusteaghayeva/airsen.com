<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use LogicException;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $password = config('airsen.admin_initial_password');

        if (! is_string($password) || strlen($password) < 12) {
            throw new LogicException('ADMIN_INITIAL_PASSWORD must contain at least 12 characters before seeding admin users.');
        }

        // 1. Turan Safarli (Founder & CEO)
        User::updateOrCreate(
            ['email' => 'admin@airsen.com'],
            [
                'name' => 'Turan Safarli',
                'password' => Hash::make($password),
                'role' => 'super_admin',
                'is_active' => true,
            ]
        );

        // 2. Tamerlan Safarli (Co-Founder & CFO)
        User::updateOrCreate(
            ['email' => 'tamerlan@airsen.com'],
            [
                'name' => 'Tamerlan Safarli',
                'password' => Hash::make($password),
                'role' => 'super_admin',
                'is_active' => true,
            ]
        );

        // 3. Demo Moderator Account
        User::updateOrCreate(
            ['email' => 'moderator@airsen.com'],
            [
                'name' => 'AirSen Moderator',
                'password' => Hash::make('moderator12345678?'),
                'role' => 'moderator',
                'is_active' => true,
            ]
        );
    }
}
