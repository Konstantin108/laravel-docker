<?php

declare(strict_types=1);

namespace App\Services\Contracts;

interface SearchableSourceContract
{
    // TODO kpstya необходимо добавить базовую реализацию

    public function getId(): int;

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
