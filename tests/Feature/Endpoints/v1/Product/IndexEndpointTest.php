<?php

namespace Tests\Feature\Endpoints\v1\Product;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

final class IndexEndpointTest extends TestCase
{
    use RefreshDatabase;

    private const ROUTE = 'api.v1.products.index';

    #[Test]
    public function it_returns_products_list_when_no_params_provided()
    {
        $count = 3;
        Product::factory()->count($count)->create();

        $response = $this->getJson(route(self::ROUTE))
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'category_name',
                        'price',
                        'category_id',
                        'description',
                        'category_description',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ])
            ->assertHeader('Content-Type', 'application/json')
            ->assertOk();

        $this->assertCount($count, $response->json('data'));
    }

    #[Test]
    #[TestWith(data: ['xiaom', 1])]
    #[TestWith(data: ['автоваз', 0])]
    #[TestWith(data: ['0000', 2])]
    #[TestWith(data: ['цена', 1])]
    #[TestWith(data: ['sa', 3])]
    #[TestWith(data: [null, 3])]
    public function it_filters_products_by_search_param(?string $search, int $resultCount): void
    {
        $payload = [
            ['name' => 'Xiaomi', 'description' => 'лучшая цена на рынке', 'price' => 1500000],
            ['name' => 'samsung', 'description' => 'корейское качество', 'price' => 950000],
            ['name' => 'электрогитара', 'description' => 'отличный звук', 'price' => 999900],
        ];

        foreach ($payload as $elem) {
            Product::factory()
                ->withName($elem['name'])
                ->withDescription($elem['description'])
                ->withPrice($elem['price'])
                ->create();
        }

        $response = $this->getJson(route(self::ROUTE, [
            'search' => $search,
        ]))->assertOk();

        $this->assertCount($resultCount, $response->json('data'));
    }
}
