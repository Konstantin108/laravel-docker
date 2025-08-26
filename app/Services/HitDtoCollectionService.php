<?php

declare(strict_types=1);

namespace App\Services;

use App\Dto\Contracts\HitDtoContract;
use App\Exceptions\SearchIndexDoesNotExist;
use App\Factories\Contracts\HitDtoFactoryContract;
use Illuminate\Support\Collection;

class HitDtoCollectionService
{
    /**
     * @var List<HitDtoFactoryContract>
     */
    private array $factories;

    public function __construct(HitDtoFactoryContract ...$factories)
    {
        $this->factories = $factories;
    }

    /**
     * @param  array<string, mixed>  $hits
     * @return Collection<string, HitDtoContract>
     *
     * @throws SearchIndexDoesNotExist
     */
    public function create(array $hits): Collection
    {
        // TODO проверить, если в массиве разные entity или фабрики для entity не существует

        return collect(array_map(function (array $hit): HitDtoContract {
            $index = $hit['_index'];
            if (! isset($this->factories[$index])) {
                throw SearchIndexDoesNotExist::buildMessage($index);
            }

            return $this->factories[$index]->createFromArray($hit['_source']);
        }, $hits));
    }
}
