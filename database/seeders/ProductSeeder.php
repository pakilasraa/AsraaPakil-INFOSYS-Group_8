<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $map = Category::pluck('id','name');

        $products = [
            ['name' => 'Americano', 'price' => 110, 'category' => 'Coffee'],
            ['name' => 'Latte', 'price' => 140, 'category' => 'Coffee'],
            ['name' => 'Cappuccino', 'price' => 140, 'category' => 'Coffee'],
            ['name' => 'Green Tea', 'price' => 95, 'category' => 'Tea'],
            ['name' => 'Milk Tea', 'price' => 120, 'category' => 'Tea'],
            ['name' => 'Croissant', 'price' => 85, 'category' => 'Pastries'],
            ['name' => 'Blueberry Muffin', 'price' => 75, 'category' => 'Pastries'],
            ['name' => 'Ham & Cheese Sandwich', 'price' => 150, 'category' => 'Sandwiches'],
            ['name' => 'Tropical Smoothie', 'price' => 130, 'category' => 'Non-Coffee'],
        ];

        foreach ($products as $p) {
            $categoryId = $map[$p['category']] ?? null;
            if (!$categoryId) { continue; }
            Product::firstOrCreate(
                ['name' => $p['name'], 'category_id' => $categoryId],
                ['price' => $p['price'], 'image' => null]
            );
        }
    }
}


