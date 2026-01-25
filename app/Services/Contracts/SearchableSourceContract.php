<?php

declare(strict_types=1);

namespace App\Services\Contracts;

interface SearchableSourceContract
{
    public function getId(): int;

    // TODO kpstya написасть тесты для api/v2/product

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
