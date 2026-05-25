<?php

namespace Tests\Unit\Mappers;

use App\Enums\SortedByEnum;
use App\Services\Elasticsearch\PaginationRequestMapper;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ValueError;

final class PaginationRequestMapperTest extends TestCase
{
    #[Test]
    public function it_maps_default_correctly(): void
    {
        $paginationRequestDto = (new PaginationRequestMapper)->map();

        $this->assertSame(15, $paginationRequestDto->size);
        $this->assertSame(0, $paginationRequestDto->from);
        $this->assertSame(SortedByEnum::DESC, $paginationRequestDto->sort);
        $this->assertNull($paginationRequestDto->search);
    }

    #[Test]
    public function it_maps_from_correctly_for_second_page(): void
    {
        $paginationRequestDto = (new PaginationRequestMapper)->map(null, 12, null, 2);

        $this->assertSame(12, $paginationRequestDto->size);
        $this->assertSame(12, $paginationRequestDto->from);
        $this->assertSame(SortedByEnum::DESC, $paginationRequestDto->sort);
        $this->assertNull($paginationRequestDto->search);
    }

    #[Test]
    public function it_maps_from_correctly(): void
    {
        $searchedString = 'searched';
        $paginationRequestDto = (new PaginationRequestMapper)->map($searchedString, 25, null, 3);

        $this->assertSame(25, $paginationRequestDto->size);
        $this->assertSame(50, $paginationRequestDto->from);
        $this->assertSame(SortedByEnum::DESC, $paginationRequestDto->sort);
        $this->assertSame($searchedString, $paginationRequestDto->search);
    }

    #[Test]
    public function it_maps_from_correctly_sorted_by_asc(): void
    {
        $paginationRequestDto = (new PaginationRequestMapper)->map(null, 25, 'asc', 3);

        $this->assertSame(25, $paginationRequestDto->size);
        $this->assertSame(50, $paginationRequestDto->from);
        $this->assertSame(SortedByEnum::ASC, $paginationRequestDto->sort);
        $this->assertNull($paginationRequestDto->search);
    }

    #[Test]
    public function it_maps_from_correctly_sorted_by_desc(): void
    {
        $paginationRequestDto = (new PaginationRequestMapper)->map(null, 25, 'desc', 3);

        $this->assertSame(25, $paginationRequestDto->size);
        $this->assertSame(50, $paginationRequestDto->from);
        $this->assertSame(SortedByEnum::DESC, $paginationRequestDto->sort);
        $this->assertNull($paginationRequestDto->search);
    }

    #[Test]
    public function it_throws_exception_when_sorted_by_is_invalid(): void
    {
        $this->expectException(ValueError::class);

        (new PaginationRequestMapper)->map(null, 25, 'invalid', 3);
    }
}
