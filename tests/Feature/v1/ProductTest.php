<?php

namespace Tests\Feature\v1;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

final class ProductTest extends TestCase
{
    use RefreshDatabase;

    private const INDEX_ROUTE = 'api.v1.product.index';

    public function test_product_index_v1_without_params()
    {
        $count = 3;
        Product::factory()->count($count)->create();

        $response = $this->getJson(route(self::INDEX_ROUTE))
            ->assertOk()
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
            ]);

        $this->assertCount($count, $response->json('data'));
    }

    #[TestWith(['xiaom', 1])]
    #[TestWith(['автоваз', 0])]
    #[TestWith(['0000', 2])]
    #[TestWith(['цена', 1])]
    #[TestWith(['sa', 3])]
    #[TestWith([null, 3])]
    public function test_product_index_v1_with_search_param(?string $search, int $resultCount): void
    {
        $data = [
            ['name' => 'Xiaomi', 'description' => 'лучшая цена на рынке', 'price' => 1500000],
            ['name' => 'samsung', 'description' => 'корейское качество', 'price' => 950000],
            ['name' => 'электрогитара', 'description' => 'отличный звук', 'price' => 999900],
        ];

        Product::factory()
            ->count(count($data))
            ->state(new Sequence(...$data))
            ->create();

        $response = $this->getJson(route(self::INDEX_ROUTE, [
            'search' => $search,
        ]))->assertOk();

        $this->assertCount($resultCount, $response->json('data'));
    }
}
