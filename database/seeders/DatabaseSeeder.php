<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * 
     * This runs all the seeders in order
     */
    public function run(): void
    {
        // Run seeders in order (users first, then categories, packages, then listings)
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            PackageSeeder::class,
            ListingSeeder::class,
        ]);
    }
}