<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Coffee', 'description' => 'Hot and iced coffee beverages'],
            ['name' => 'Tea', 'description' => 'Tea-based drinks and infusions'],
            ['name' => 'Pastries', 'description' => 'Freshly baked goods'],
            ['name' => 'Sandwiches', 'description' => 'Savory sandwiches and paninis'],
            ['name' => 'Non-Coffee', 'description' => 'Smoothies, juices, and more'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(['name' => $cat['name']], $cat);
        }
    }
}


