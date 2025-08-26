<?php

declare(strict_types=1);

namespace App\Factories\Contracts;

use App\Dto\Contracts\HitDtoContract;

interface HitDtoFactoryContract
{
    /**
     * @param  array<string, mixed>  $hit
     */
    public function createFromArray(array $hit): HitDtoContract;
}
