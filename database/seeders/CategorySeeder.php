<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Creates initial categories for the classifieds site
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Stanovi',
                'description' => 'Prodaja i izdavanje stanova',
                'icon' => 'building',
                'order' => 1,
            ],
            [
                'name' => 'Kuće',
                'description' => 'Prodaja i izdavanje kuća',
                'icon' => 'home',
                'order' => 2,
            ],
            [
                'name' => 'Zemljište',
                'description' => 'Prodaja zemljišta',
                'icon' => 'map',
                'order' => 3,
            ],
            [
                'name' => 'Poslovni prostor',
                'description' => 'Kancelarije, lokali, magacini',
                'icon' => 'briefcase',
                'order' => 4,
            ],
            [
                'name' => 'Garaže',
                'description' => 'Prodaja i izdavanje garaža',
                'icon' => 'car',
                'order' => 5,
            ],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['slug' => Str::slug($category['name'])],
                [
                    'name' => $category['name'],
                    'description' => $category['description'],
                    'icon' => $category['icon'],
                    'order' => $category['order'],
                    'is_active' => true,
                ]
            );
        }
    }
}