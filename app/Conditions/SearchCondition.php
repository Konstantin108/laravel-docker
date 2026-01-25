<?php

declare(strict_types=1);

namespace App\Conditions;

final class SearchCondition
{
    private const MIN_LENGTH = 3;

    public static function isSatisfiedBy(?string $value = null): bool
    {
        return $value !== null && mb_strlen($value) >= self::MIN_LENGTH;
    }
}
