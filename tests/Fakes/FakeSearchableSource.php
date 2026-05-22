<?php

namespace Tests\Fakes;

use App\Services\Contracts\SearchableSourceContract;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Data;

final class FakeSearchableSource extends Data implements SearchableSourceContract
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly ?Carbon $createdAt = null,
        public readonly ?Carbon $updatedAt = null,
    ) {}

    public function getId(): int
    {
        return $this->id;
    }
}
