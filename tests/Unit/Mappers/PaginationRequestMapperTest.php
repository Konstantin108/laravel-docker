<?php

namespace Tests\Unit\Mappers;

use App\Services\Elasticsearch\PaginationRequestMapper;
use PHPUnit\Framework\TestCase;

final class PaginationRequestMapperTest extends TestCase
{
    public function test_it_maps_default_correctly()
    {
        $paginationRequestDto = (new PaginationRequestMapper)->map();

        $this->assertSame(10, $paginationRequestDto->size);
        $this->assertSame(0, $paginationRequestDto->from);
        $this->assertNull($paginationRequestDto->search);
    }

    public function test_it_calculates_from_correctly_for_second_page()
    {
        $paginationRequestDto = (new PaginationRequestMapper)->map(null, 10, 2);

        $this->assertSame(10, $paginationRequestDto->size);
        $this->assertSame(10, $paginationRequestDto->from);
        $this->assertNull($paginationRequestDto->search);
    }

    public function test_it_calculates_from_correctly()
    {
        $searchedString = 'searched';
        $paginationRequestDto = (new PaginationRequestMapper)->map($searchedString, 25, 3);

        $this->assertSame(25, $paginationRequestDto->size);
        $this->assertSame(50, $paginationRequestDto->from);
        $this->assertSame($searchedString, $paginationRequestDto->search);
    }
}
