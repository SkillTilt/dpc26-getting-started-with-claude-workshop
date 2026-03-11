<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Seed the categories table.
     */
    public function run(): void
    {
        $categories = [
            ['id' => 1, 'name' => 'Electronics', 'slug' => 'electronics', 'icon' => 'laptop'],
            ['id' => 2, 'name' => 'Vintage & Collectibles', 'slug' => 'vintage-collectibles', 'icon' => 'camera'],
            ['id' => 3, 'name' => 'Sports & Outdoors', 'slug' => 'sports-outdoors', 'icon' => 'bicycle'],
            ['id' => 4, 'name' => 'Home & Garden', 'slug' => 'home-garden', 'icon' => 'leaf'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
