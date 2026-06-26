<?php

declare(strict_types=1);

namespace App\Services\Contracts;

interface SearchableSourceContract
{
    public function getId(): int;

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}

/* TODO kpstya
    - для чего нужна таблица notifications
    - надо перевести админку на русский язык */
