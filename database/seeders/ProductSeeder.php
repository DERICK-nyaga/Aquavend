<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            // Water
            ['name' => 'Water', 'category' => 'water', 'size_value' => 500, 'unit' => 'ml', 'price' => 20],
            ['name' => 'Water', 'category' => 'water', 'size_value' => 1, 'unit' => 'l', 'price' => 50],
            ['name' => 'Water', 'category' => 'water', 'size_value' => 1.5, 'unit' => 'l', 'price' => 70],
            ['name' => 'Water', 'category' => 'water', 'size_value' => 5, 'unit' => 'l', 'price' => 150],
            ['name' => 'Water', 'category' => 'water', 'size_value' => 10, 'unit' => 'l', 'price' => 280],
            ['name' => 'Water', 'category' => 'water', 'size_value' => 20, 'unit' => 'l', 'price' => 500],

            // Oil
            ['name' => 'Cooking Oil', 'category' => 'oil', 'size_value' => 500, 'unit' => 'ml', 'price' => 150],
            ['name' => 'Cooking Oil', 'category' => 'oil', 'size_value' => 1, 'unit' => 'l', 'price' => 280],
            ['name' => 'Cooking Oil', 'category' => 'oil', 'size_value' => 5, 'unit' => 'l', 'price' => 1300],

            // Milk
            ['name' => 'Fresh Milk', 'category' => 'milk', 'size_value' => 500, 'unit' => 'ml', 'price' => 60],
            ['name' => 'Fresh Milk', 'category' => 'milk', 'size_value' => 1, 'unit' => 'l', 'price' => 110],

            // Yoghurt
            ['name' => 'Yoghurt', 'category' => 'yoghurt', 'size_value' => 500, 'unit' => 'ml', 'price' => 90],
            ['name' => 'Yoghurt', 'category' => 'yoghurt', 'size_value' => 1, 'unit' => 'l', 'price' => 170],

            // Eggs
            ['name' => 'Eggs', 'category' => 'eggs', 'size_value' => 1, 'unit' => 'tray', 'price' => 420],
            ['name' => 'Eggs', 'category' => 'eggs', 'size_value' => 1, 'unit' => 'dozen', 'price' => 180],

            // Bottles (empty containers for refill)
            ['name' => 'Bottle', 'category' => 'bottles', 'size_value' => 20, 'unit' => 'l', 'price' => 300],
            ['name' => 'Bottle', 'category' => 'bottles', 'size_value' => 10, 'unit' => 'l', 'price' => 200],
        ];

        foreach ($items as $item) {
            Product::create($item + ['is_active' => true]);
        }
    }
}