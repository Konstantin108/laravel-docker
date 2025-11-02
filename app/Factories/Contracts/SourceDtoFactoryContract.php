<?php

declare(strict_types=1);

namespace App\Factories\Contracts;

use App\Entities\User\Contracts\SearchableSourceContract;

interface SourceDtoFactoryContract
{
    /**
     * @param  array<string, mixed>  $source
     */
    public function createFromArray(array $source): SearchableSourceContract;
}
