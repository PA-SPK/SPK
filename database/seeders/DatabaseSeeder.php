<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Production default: empty — data is loaded via the Import feature.
     * To seed the Puskesmas case study data, run: php artisan db:seed --class=SPKDataSeeder
     */
    public function run(): void
    {
        // No default seed — the app starts empty.
        // Users import their own data via the Excel/CSV import feature.
    }
}

