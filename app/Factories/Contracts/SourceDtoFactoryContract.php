<?php

declare(strict_types=1);

namespace App\Factories\Contracts;

use App\Services\Contracts\SearchableSourceContract;

// TODO kpstya что за правки мне внес ИИ

interface SourceDtoFactoryContract
{
    /**
     * @param  array<string, mixed>  $source
     */
    public function make(array $source): SearchableSourceContract;
}
