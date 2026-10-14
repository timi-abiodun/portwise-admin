<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HsCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lines = [
            'phones-electronics' => [
                ['8517.13', 'Smartphones'],
                ['8471.30', 'Laptops and portable computers'],
                ['8504.40', 'Chargers and power adapters'],
            ],
            'textiles-fabrics' => [
                ['6109.10', 'Cotton T-shirts'],
            ],
            'cosmetics' => [
                ['3304.99', 'Skincare and beauty preparations'],
                ['3305.10', 'Shampoos'],
            ],
            'machine-parts' => [
                ['8413.70', 'Centrifugal pumps'],
                ['8482.10', 'Ball bearings'],
            ],
            'footwear' => [
                ['6403.99', 'Leather footwear'],
                ['6404.11', 'Sports footwear and sneakers'],
            ],
        ];

        foreach ($lines as $slug => $items) {
            $categoryId = \App\Models\Category::where('slug', $slug)->value('id');
            foreach ($items as [$code, $description]) {
                \App\Models\HsCode::updateOrCreate(
                    ['code' => $code],
                    ['category_id' => $categoryId, 'description' => $description, 'is_active' => true]
                );
            }
        }
    }
}
