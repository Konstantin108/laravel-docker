<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => ProductCategory::factory(),
            'name' => $this->faker->unique()->word(),
            'description' => $this->faker->optional()->sentence(),
            'price' => $this->faker->numberBetween(100000, 1500000),
        ];
    }
}
