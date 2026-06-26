<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

final class ProductCategorySeeder extends Seeder
{
    private const PRODUCT_CATEGORIES_COUNT = 3;

    public function run(): void
    {
        ProductCategory::factory()
            ->count(self::PRODUCT_CATEGORIES_COUNT)
            ->create();
    }
}

// TODO kpstya нужен ли строгий тип для моделей и реквестов
