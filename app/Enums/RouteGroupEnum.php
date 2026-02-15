<?php

declare(strict_types=1);

namespace App\Enums;

enum RouteGroupEnum: string
{
    case USER = 'user';

    case PRODUCT = 'product';

    public const DESCRIPTIONS = [
        self::USER->value => 'Эндпоинты для работы с пользователями',
        self::PRODUCT->value => 'Эндпоинты для работы с продуктами',
    ];
}
