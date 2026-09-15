<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Setting::set('google_play_status', 'moderation');
        Setting::set('google_play_url', 'https://play.google.com/store/apps/details?id=com.airsen.app');

        $this->call(AdminUserSeeder::class);
    }
}
