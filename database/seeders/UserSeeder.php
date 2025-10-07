<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Creates an admin user and a few regular users for testing
     */
    public function run(): void
    {
        // Create admin user (only if doesn't exist)
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
            'name' => 'Admin',
            'password' => Hash::make('password'), // CHANGE THIS IN PRODUCTION!
            'phone' => '+381 11 1234567',
            'city' => 'Beograd',
            'role' => 'admin',
            'is_verified' => true,
            'is_active' => true,
            'email_verified_at' => now(),
            ]
        );

        // Create a regular user for testing (only if doesn't exist)
        User::firstOrCreate(
            ['email' => 'petar@example.com'],
            [
            'name' => 'Petar Petrović',
            'password' => Hash::make('password'),
            'phone' => '+381 11 7654321',
            'city' => 'Beograd',
            'bio' => 'Prodajem nekretnine u Beogradu',
            'role' => 'user',
            'is_verified' => true,
            'is_active' => true,
            'email_verified_at' => now(),
            ]
        );

        // Create another regular user (only if doesn't exist)
        User::firstOrCreate(
            ['email' => 'marija@example.com'],
            [
            'name' => 'Marija Marković',
            'password' => Hash::make('password'),
            'phone' => '+381 21 1234567',
            'city' => 'Novi Sad',
            'bio' => 'Agent za nekretnine',
            'role' => 'user',
            'is_verified' => true,
            'is_active' => true,
            'email_verified_at' => now(),
            ]
        );
    }
}