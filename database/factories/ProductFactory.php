<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
final class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => ProductCategory::factory(),
            'name' => $this->faker->unique()->bothify('????????##'),
            'price' => $this->faker->numberBetween(100000, 1500000),
        ];
    }

    public function withName(string $name): self
    {
        return $this->state(['name' => $name]);
    }

    public function withPrice(int $price): self
    {
        return $this->state(['price' => $price]);
    }

    public function withDescription(?string $description = null): self
    {
        return $this->state([
            'description' => $description ?? $this->faker->text(),
        ]);
    }
}
