<?php

namespace Database\Seeders;

use App\Models\Listing;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ListingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get users and categories
        $users = User::where('role', 'user')->get();
        $categories = Category::all();

        if ($users->isEmpty() || $categories->isEmpty()) {
            $this->command->warn('Please run UserSeeder and CategorySeeder first!');
            return;
        }

        // Sample listings data (simplified without features for now)
        $listings = [
            [
                'title' => 'Luksuzan trosoban stan u centru Beograda',
                'description' => 'Prelepo opremljen trosoban stan u srcu Beograda. Stan se nalazi na 5. spratu zgrade sa liftom. Kompletno renoviran 2023. godine. Sastoji se od dnevnog boravka, trpezarije, kuhinje, tri spavaće sobe, dva kupatila i terase. Orjentacija jug-zapad. Parking mesto u garaži. Idealan za porodicu.',
                'price' => 150000,
                'currency' => 'EUR',
                'city' => 'Beograd',
                'municipality' => 'Stari Grad',
                'address' => 'Kralja Petra 25',
                'area' => 85,
                'rooms' => 3,
                'bathrooms' => 2,
                'floor' => 5,
                'total_floors' => 8,
                'year_built' => 2010,
                'listing_type' => 'sale',
                'is_featured' => true,
                'features' => ['Parking', 'Lift', 'Balkon', 'Klima', 'Centralno grejanje', 'Renoviran'],
            ],
            [
                'title' => 'Dvosoban stan Novi Beograd',
                'description' => 'Prodajem dvosoban stan u Novom Beogradu, blok 70. Stan je na 12. spratu, orijentisan prema jugu sa prelepim pogledom. Kompletno renoviran. Odlična lokacija sa razvijenom infrastrukturom. Blizina marketa, škola, vrtića.',
                'price' => 120000,
                'currency' => 'EUR',
                'city' => 'Beograd',
                'municipality' => 'Novi Beograd',
                'area' => 62,
                'rooms' => 2,
                'bathrooms' => 1,
                'floor' => 12,
                'total_floors' => 15,
                'year_built' => 2015,
                'listing_type' => 'sale',
                'is_top' => true,
            ],
            [
                'title' => 'Moderna kuća sa bazenom Dedinje',
                'description' => 'Ekskluzivna moderna kuća na Dedinju. Kuća ima 350m2 na parceli od 800m2. Sastoji se od prizemlja i sprata. 5 spavaćih soba, 4 kupatila, prostran dnevni boravak, kuhinja, trpezarija. Bazen 10x5m. Video nadzor, alarm, garaža za dva automobila.',
                'price' => 650000,
                'currency' => 'EUR',
                'city' => 'Beograd',
                'municipality' => 'Savski Venac',
                'area' => 350,
                'rooms' => 5,
                'bathrooms' => 4,
                'year_built' => 2020,
                'listing_type' => 'sale',
                'is_featured' => true,
            ],
            [
                'title' => 'Jednosoban stan Vračar',
                'description' => 'Prodajem jednosoban stan na Vračaru, u blizini Cvetnog trga. Stan je kompletno renoviran, svetao i prostran. Idealan za mlade parove ili studente. Odlična lokacija sa svim sadržajima.',
                'price' => 85000,
                'currency' => 'EUR',
                'city' => 'Beograd',
                'municipality' => 'Vračar',
                'area' => 42,
                'rooms' => 1,
                'bathrooms' => 1,
                'floor' => 3,
                'total_floors' => 5,
                'year_built' => 1985,
                'listing_type' => 'sale',
            ],
            [
                'title' => 'Trosoban stan Zemun',
                'description' => 'Izdajem namešten trosoban stan u Zemunu. Stan se nalazi u novijoj zgradi sa liftom. Kompletno opremljen nameštajem i aparatima. Parking mesto ispred zgrade. Mirna lokacija.',
                'price' => 600,
                'currency' => 'EUR',
                'city' => 'Beograd',
                'municipality' => 'Zemun',
                'area' => 75,
                'rooms' => 3,
                'bathrooms' => 2,
                'floor' => 4,
                'total_floors' => 6,
                'year_built' => 2018,
                'listing_type' => 'rent',
                'is_top' => true,
            ],
            [
                'title' => 'Poslovni prostor u centru Novog Sada',
                'description' => 'Izdajem poslovni prostor u centru Novog Sada, idealan za kancelariju ili ordinaciju. Površina 50m2, prizemlje sa izlogom. Odlična lokacija sa velikom frekventnom zonom.',
                'price' => 800,
                'currency' => 'EUR',
                'city' => 'Novi Sad',
                'area' => 50,
                'floor' => 0,
                'year_built' => 2005,
                'listing_type' => 'rent',
            ],
            [
                'title' => 'Četvorosoban stan Banovo Brdo',
                'description' => 'Prodajem četvorosoban stan na Banovom Brdu. Stan je u odličnom stanju, svetao i prostran. Sastoji se od dnevnog boravka, kuhinje, trpezarije, 4 spavaće sobe i 2 kupatila. Parking mesto u dvorištu.',
                'price' => 165000,
                'currency' => 'EUR',
                'city' => 'Beograd',
                'municipality' => 'Čukarica',
                'area' => 95,
                'rooms' => 4,
                'bathrooms' => 2,
                'floor' => 2,
                'total_floors' => 4,
                'year_built' => 2000,
                'listing_type' => 'sale',
                'is_featured' => true,
            ],
            [
                'title' => 'Građevinsko zemljište Avala',
                'description' => 'Prodajem građevinsko zemljište na Avali. Parcela od 1200m2 sa mogućnošću izgradnje kuće. Sva infrastruktura na parceli. Mirna lokacija sa prelepim pogledom.',
                'price' => 85000,
                'currency' => 'EUR',
                'city' => 'Beograd',
                'area' => 1200,
                'listing_type' => 'sale',
            ],
            [
                'title' => 'Lux penthouse Dorćol',
                'description' => 'Penthouse sa terasom od 80m2 na Dorćolu. Kompletno opremljen sa vrhunskim nameštajem i tehnikom. Garaža za 2 automobila. Unikatna nekretnina.',
                'price' => 380000,
                'currency' => 'EUR',
                'city' => 'Beograd',
                'municipality' => 'Stari Grad',
                'area' => 145,
                'rooms' => 4,
                'bathrooms' => 3,
                'floor' => 6,
                'total_floors' => 6,
                'year_built' => 2021,
                'listing_type' => 'sale',
                'is_featured' => true,
            ],
            [
                'title' => 'Garsonjera Novi Sad centar',
                'description' => 'Kompletno namještena garsonjera u strogom centru Novog Sada. Idealna za studente ili mlade parove. Svi računi uključeni u cenu.',
                'price' => 300,
                'currency' => 'EUR',
                'city' => 'Novi Sad',
                'area' => 28,
                'rooms' => 1,
                'bathrooms' => 1,
                'floor' => 2,
                'total_floors' => 4,
                'year_built' => 2010,
                'listing_type' => 'rent',
            ],
        ];

        foreach ($listings as $index => $listingData) {
            // Assign random user and category
            $user = $users->random();
            $category = $categories->random();

            // Calculate featured/top until dates
            $featuredUntil = null;
            $topUntil = null;
            
            if (isset($listingData['is_featured']) && $listingData['is_featured']) {
                $featuredUntil = now()->addDays(30);
            }
            
            if (isset($listingData['is_top']) && $listingData['is_top']) {
                $topUntil = now()->addDays(15);
            }

            Listing::create([
                'user_id' => $user->id,
                'category_id' => $category->id,
                'title' => $listingData['title'],
                'slug' => Str::slug($listingData['title']) . '-' . Str::random(6),
                'description' => $listingData['description'],
                'price' => $listingData['price'],
                'currency' => $listingData['currency'],
                'city' => $listingData['city'],
                'municipality' => $listingData['municipality'] ?? null,
                'address' => $listingData['address'] ?? null,
                'area' => $listingData['area'] ?? null,
                'rooms' => $listingData['rooms'] ?? null,
                'bathrooms' => $listingData['bathrooms'] ?? null,
                'floor' => $listingData['floor'] ?? null,
                'total_floors' => $listingData['total_floors'] ?? null,
                'year_built' => $listingData['year_built'] ?? null,
                'listing_type' => $listingData['listing_type'],
                'status' => 'active',
                'is_featured' => $listingData['is_featured'] ?? false,
                'featured_until' => $featuredUntil,
                'is_top' => $listingData['is_top'] ?? false,
                'top_until' => $topUntil,
                'contact_name' => $user->name,
                'contact_phone' => $user->phone ?? '+381 11 1234567',
                'contact_email' => $user->email,
                'published_at' => now()->subDays(rand(1, 30)),
                'views' => rand(10, 500),
            ]);

            $this->command->info("Created: {$listingData['title']}");
        }

        $this->command->info('✅ Created 10 sample listings successfully!');
    }
}