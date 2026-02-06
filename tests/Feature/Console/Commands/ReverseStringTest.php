<?php

namespace Tests\Feature\Console\Commands;

use PHPUnit\Framework\Attributes\TestWith;
use Symfony\Component\Console\Exception\RuntimeException;
use Tests\TestCase;

final class ReverseStringTest extends TestCase
{
    private const COMMAND = 'app:reverse-string';

    #[TestWith(['12345', '54321'])]
    #[TestWith(['привет', 'тевирп'])]
    #[TestWith(['hello', 'olleh'])]
    #[TestWith(['こんにちは', 'はちにんこ'])]
    #[TestWith(['안녕하세요', '요세하녕안'])]
    public function test_it_reverses_string_successfully_when_argument_passed(
        string $string,
        string $reversedString
    ): void {
        $this->artisan(self::COMMAND, [
            'string:string' => $string,
        ])
            ->expectsOutput($reversedString)
            ->assertSuccessful();
    }

    public function test_it_returns_error_when_reversing_string_without_argument(): void
    {
        $this->expectException(RuntimeException::class);

        $this->artisan(self::COMMAND);
    }
}
