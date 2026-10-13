<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Phones & Electronics', 'slug' => 'phones-electronics', 'description' => 'Smartphones, tablets, laptops, accessories'],
            ['name' => 'Textiles & Fabrics',   'slug' => 'textiles-fabrics',   'description' => 'Ankara, lace, cotton and synthetic fabrics'],
            ['name' => 'Cosmetics',            'slug' => 'cosmetics',          'description' => 'Skincare, makeup, hair products'],
            ['name' => 'Machine Parts',        'slug' => 'machine-parts',      'description' => 'Industrial and automotive spare parts'],
            ['name' => 'Footwear',             'slug' => 'footwear',           'description' => 'Shoes, sandals, sneakers'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(['slug' => $category['slug']], $category);
        }
    }
}