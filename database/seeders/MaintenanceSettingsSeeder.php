<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MaintenanceSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Setting::updateOrCreate(
            ['key' => 'maintenance_monitored_folders'],
            [
                'value' => '3d-models, articles, projects, services, settings, team',
                'type' => 'text'
            ]
        );
    }
}
