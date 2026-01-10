<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class ProductCategorySeeder extends Seeder
{
    private const PRODUCT_CATEGORIES_COUNT = 3;

    public function run(): void
    {
        $existingProductCategoriesCount = ProductCategory::query()->count();

        if ($existingProductCategoriesCount >= self::PRODUCT_CATEGORIES_COUNT) {
            return;
        }

        ProductCategory::factory()
            ->count(self::PRODUCT_CATEGORIES_COUNT - $existingProductCategoriesCount)
            ->create();
    }
}
