<?php

declare(strict_types=1);

namespace App\Clients\Elasticsearch\Dto;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
final class SettingsDto extends Data
{
    public function __construct(
        public readonly int $timeout,
        public readonly int $connectTimeout,
        public readonly int $retryTimes,
        public readonly int $retrySleepMilliseconds,
    ) {}
}
