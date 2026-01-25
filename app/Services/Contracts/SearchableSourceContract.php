<?php

declare(strict_types=1);

namespace App\Services\Contracts;

interface SearchableSourceContract
{
    public function getId(): int;

    /* TODO kpstya
        - products, product_categories - добавить эндпоинты v1 и v2
        - написасть тесты
        - добавить логику работы с Elasticsearch
        - обновить тесты для команд Elasticsearch */

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
