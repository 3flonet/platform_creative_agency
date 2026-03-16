<?php

namespace Database\Seeders;

use App\Models\User;
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
        \App\Models\User::updateOrCreate(
            ['email' => 'admin@3flo.net'],
            [
                'name' => '3FLO Admin',
                'password' => bcrypt('password'),
                'role' => 'super_admin',
            ]
        );

        $this->call([
            ContentSeeder::class,
            JournalSeeder::class,
            SettingSeeder::class,
            MaintenanceSettingsSeeder::class,
        ]);
    }
}
