<?php

namespace Tests\Unit\Mappers;

use App\Services\Elasticsearch\PaginationRequestMapper;
use Tests\TestCase;

final class PaginationRequestMapperTest extends TestCase
{
    public function test_it_maps_default_correctly()
    {
        $dto = (new PaginationRequestMapper)
            ->map();

        $this->assertSame(10, $dto->size);
        $this->assertSame(0, $dto->from);
        $this->assertNull($dto->search);
    }

    public function test_it_calculates_from_correctly_for_second_page()
    {
        $dto = (new PaginationRequestMapper)
            ->map(null, 10, 2);

        $this->assertSame(10, $dto->size);
        $this->assertSame(10, $dto->from);
        $this->assertNull($dto->search);
    }

    public function test_it_calculates_from_correctly()
    {
        $searchedString = 'searched';
        $dto = (new PaginationRequestMapper)
            ->map($searchedString, 25, 3);

        $this->assertSame(25, $dto->size);
        $this->assertSame(50, $dto->from);
        $this->assertSame($searchedString, $dto->search);
    }
}
