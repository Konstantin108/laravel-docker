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

        $existingProductsCount = Product::query()->count();

        if ($existingProductsCount >= self::PRODUCTS_COUNT) {
            return;
        }

        Product::factory()
            ->count(self::PRODUCTS_COUNT - $existingProductsCount)
            ->recycle($categories)
            ->create();
    }
}
