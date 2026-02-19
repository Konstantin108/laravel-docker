<?php

namespace Tests\Integration\Repositories;

use App\Models\Product;
use App\Repositories\Product\ProductEloquentRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class ProductEloquentRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private ProductEloquentRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new ProductEloquentRepository;
    }

    #[Test]
    public function it_returns_all_products(): void
    {
        $count = 2;
        Product::factory()->count($count)->create();

        $result = $this->repository->getAllProducts();

        $this->assertCount($count, $result);
    }

    #[Test]
    public function it_returns_all_products_when_limit_param_is_given(): void
    {
        Product::factory()->count(5)->create();
        $limit = 3;

        $result = $this->repository->getAllProducts(limit: $limit);

        $this->assertCount($limit, $result);
    }
}
