<?php

namespace Tests\Unit\Mappers;

use App\Services\Elasticsearch\PaginationRequestMapper;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class PaginationRequestMapperTest extends TestCase
{
    #[Test]
    public function it_maps_default_correctly(): void
    {
        $paginationRequestDto = (new PaginationRequestMapper)->map();

        $this->assertSame(15, $paginationRequestDto->size);
        $this->assertSame(0, $paginationRequestDto->from);
        $this->assertNull($paginationRequestDto->search);
    }

    #[Test]
    public function it_maps_from_correctly_for_second_page(): void
    {
        $paginationRequestDto = (new PaginationRequestMapper)->map(null, 12, 2);

        $this->assertSame(12, $paginationRequestDto->size);
        $this->assertSame(12, $paginationRequestDto->from);
        $this->assertNull($paginationRequestDto->search);
    }

    #[Test]
    public function it_maps_from_correctly(): void
    {
        $searchedString = 'searched';
        $paginationRequestDto = (new PaginationRequestMapper)->map($searchedString, 25, 3);

        $this->assertSame(25, $paginationRequestDto->size);
        $this->assertSame(50, $paginationRequestDto->from);
        $this->assertSame($searchedString, $paginationRequestDto->search);
    }
}
