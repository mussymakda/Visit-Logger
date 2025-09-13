<?php

namespace Database\Seeders;

use App\Models\Settings;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Settings::firstOrCreate([], [
            'app_name' => 'Visit Logger',
            'app_logo' => null,
            'favicon' => null,
            'footer_text' => null,
        ]);
    }
}
