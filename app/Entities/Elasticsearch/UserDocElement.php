<?php

declare(strict_types=1);

namespace App\Entities\Elasticsearch;

use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapOutputName(SnakeCaseMapper::class)]
final class UserDocElement extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
        public ?string $reserveEmail = null,
        public ?string $phone = null,
        public ?string $telegram = null,
    ) {}
}
