<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packages = [
            // Top Listing Packages (appear first in search results)
            [
                'name' => '7 dana - Top oglas',
                'slug' => '7-days-top',
                'description' => 'Vaš oglas će biti prikazan na vrhu rezultata pretrage 7 dana',
                'type' => 'top',
                'duration_days' => 7,
                'price' => 500,
                'currency' => 'RSD',
                'order' => 1,
                'features' => [
                    'Prikazuje se na vrhu liste',
                    'Veća vidljivost',
                    'Više pregleda',
                    '7 dana promocije'
                ],
            ],
            [
                'name' => '15 dana - Top oglas',
                'slug' => '15-days-top',
                'description' => 'Vaš oglas će biti prikazan na vrhu rezultata pretrage 15 dana',
                'type' => 'top',
                'duration_days' => 15,
                'price' => 900,
                'currency' => 'RSD',
                'order' => 2,
                'features' => [
                    'Prikazuje se na vrhu liste',
                    'Veća vidljivost',
                    'Više pregleda',
                    '15 dana promocije',
                    'Ušteda 10%'
                ],
            ],
            [
                'name' => '30 dana - Top oglas',
                'slug' => '30-days-top',
                'description' => 'Vaš oglas će biti prikazan na vrhu rezultata pretrage 30 dana',
                'type' => 'top',
                'duration_days' => 30,
                'price' => 1500,
                'currency' => 'RSD',
                'order' => 3,
                'features' => [
                    'Prikazuje se na vrhu liste',
                    'Veća vidljivost',
                    'Više pregleda',
                    '30 dana promocije',
                    'Ušteda 25%',
                    'Najbolja vrednost'
                ],
            ],

            // Featured Listing Packages (highlighted with special design)
            [
                'name' => '7 dana - Istaknuti oglas',
                'slug' => '7-days-featured',
                'description' => 'Vaš oglas će biti istaknuto na naslovna stranici 7 dana',
                'type' => 'featured',
                'duration_days' => 7,
                'price' => 1000,
                'currency' => 'RSD',
                'order' => 4,
                'features' => [
                    'Prikazuje se na početnoj strani',
                    'Zlatni okvir',
                    'Značka "Istaknuto"',
                    'Maksimalna vidljivost',
                    '7 dana promocije'
                ],
            ],
            [
                'name' => '15 dana - Istaknuti oglas',
                'slug' => '15-days-featured',
                'description' => 'Vaš oglas će biti istaknuto na naslovna stranici 15 dana',
                'type' => 'featured',
                'duration_days' => 15,
                'price' => 1800,
                'currency' => 'RSD',
                'order' => 5,
                'features' => [
                    'Prikazuje se na početnoj strani',
                    'Zlatni okvir',
                    'Značka "Istaknuto"',
                    'Maksimalna vidljivost',
                    '15 dana promocije',
                    'Ušteda 10%'
                ],
            ],
            [
                'name' => '30 dana - Istaknuti oglas',
                'slug' => '30-days-featured',
                'description' => 'Vaš oglas će biti istaknuto na naslovna stranici 30 dana',
                'type' => 'featured',
                'duration_days' => 30,
                'price' => 3000,
                'currency' => 'RSD',
                'order' => 6,
                'features' => [
                    'Prikazuje se na početnoj strani',
                    'Zlatni okvir',
                    'Značka "Istaknuto"',
                    'Maksimalna vidljivost',
                    '30 dana promocije',
                    'Ušteda 25%',
                    'Premium paket'
                ],
            ],
        ];

        foreach ($packages as $package) {
            Package::firstOrCreate(
                ['slug' => $package['slug']],
                $package
            );
        }
    }
}