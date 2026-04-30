<?php

declare(strict_types=1);

// TODO kpstya надо в тестах переименовать $data в $payload

namespace App\Services\Contracts;

interface SearchableSourceContract
{
    public function getId(): int;

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
