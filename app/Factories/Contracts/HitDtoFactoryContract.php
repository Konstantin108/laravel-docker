<?php

declare(strict_types=1);

namespace App\Factories\Contracts;

use App\Dto\Contracts\HitDtoContract;

interface HitDtoFactoryContract
{
    /**
     * @param  array<string, mixed>  $source
     */
    public function createFromArray(array $source): HitDtoContract;
}
