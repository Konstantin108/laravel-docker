<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    private const PRODUCTS_COUNT = 20;

    public function run(): void
    {
        $categories = ProductCategory::all();
        if ($categories->isEmpty()) {
            return;
        }

        Product::factory()
            ->count(self::PRODUCTS_COUNT)
            ->recycle($categories)
            ->create();
    }
}
