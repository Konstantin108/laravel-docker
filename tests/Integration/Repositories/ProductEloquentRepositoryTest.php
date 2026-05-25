<?php

namespace Tests\Integration\Repositories;

use App\Enums\SortedByEnum;
use App\Models\Product;
use App\Repositories\Product\ProductEloquentRepository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class ProductEloquentRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private ProductEloquentRepository $repository;

    /**
     * @throws BindingResolutionException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app->make(ProductEloquentRepository::class);
    }

    #[Test]
    public function it_returns_all_products(): void
    {
        $count = 2;
        Product::factory()->count($count)->create();

        $result = $this->repository->getList();

        $this->assertInstanceOf(Product::class, $result->first());
        $this->assertCount($count, $result);
    }

    #[Test]
    public function it_returns_all_products_when_limit_param_is_given(): void
    {
        Product::factory()->count(5)->create();
        $limit = 3;

        $result = $this->repository->getList(limit: $limit);

        $this->assertCount($limit, $result);
    }

    #[test]
    public function it_returns_paginated_users_sorted_by_id_asc(): void
    {
        Product::factory()->count(5)->create();

        $ids = $this->repository->getList(sortedByEnum: SortedByEnum::ASC)
            ->pluck('id')
            ->values()
            ->all();

        $expected = $ids;
        sort($expected);

        $this->assertSame($expected, $ids);
    }

    #[test]
    public function it_returns_paginated_users_sorted_by_id_desc(): void
    {
        Product::factory()->count(5)->create();

        $ids = $this->repository->getList()->pluck('id')->values()->all();

        $expected = $ids;
        rsort($expected);

        $this->assertSame($expected, $ids);
    }
}
