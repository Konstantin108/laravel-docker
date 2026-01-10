<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class ProductCategorySeeder extends Seeder
{
    private const PRODUCT_CATEGORIES_COUNT = 3;

    public function run(): void
    {
        ProductCategory::factory()
            ->count(self::PRODUCT_CATEGORIES_COUNT)
            ->create();
    }
}
