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

    public function withName(string $name): self
    {
        return $this->state(fn (): array => ['name' => $name]);
    }

    public function withDescription(string $description): self
    {
        return $this->state(fn (): array => ['description' => $description]);
    }

    public function withPrice(int $price): self
    {
        return $this->state(fn (): array => ['price' => $price]);
    }
}
