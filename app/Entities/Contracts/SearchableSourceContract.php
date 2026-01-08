<?php

declare(strict_types=1);

namespace App\Entities\Contracts;

interface SearchableSourceContract
{
    public function getId(): int;

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
