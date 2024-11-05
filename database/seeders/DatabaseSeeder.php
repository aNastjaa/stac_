<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed the roles table
        //$this->call(RolesTableSeeder::class);

        // Call the ThemeSeeder to create a default theme
        $this->call(ThemeSeeder::class);

        //$this->call(ProUserSeeder::class);
    }
}
