<?php

namespace Tests\Feature\Console\Commands;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestWith;
use Symfony\Component\Console\Exception\RuntimeException;
use Tests\TestCase;

final class ReverseStringTest extends TestCase
{
    private const COMMAND = 'app:reverse-string';

    #[Test]
    #[TestWith(data: ['12345', '54321'])]
    #[TestWith(data: ['привет', 'тевирп'])]
    #[TestWith(data: ['hello', 'olleh'])]
    #[TestWith(data: ['こんにちは', 'はちにんこ'])]
    #[TestWith(data: ['안녕하세요', '요세하녕안'])]
    public function it_reverses_string_successfully_when_argument_passed(
        string $string,
        string $reversedString
    ): void {
        $this->artisan(self::COMMAND, [
            'string:string' => $string,
        ])
            ->expectsOutput($reversedString)
            ->assertSuccessful();
    }

    #[Test]
    public function it_returns_error_when_reversing_string_without_argument(): void
    {
        $this->expectException(RuntimeException::class);

        $this->artisan(self::COMMAND);
    }
}
