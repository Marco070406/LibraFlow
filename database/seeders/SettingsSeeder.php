<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Seed the settings table with default LibraFlow configuration.
     */
    public function run(): void
    {
        Setting::set('loan_duration_days', '14');
        Setting::set('daily_penalty', '100');
    }
}
