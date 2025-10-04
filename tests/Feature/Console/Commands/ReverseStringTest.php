<?php

declare(strict_types=1);

namespace Tests\Feature\Console\Commands;

use Symfony\Component\Console\Exception\RuntimeException;
use Tests\TestCase;

class ReverseStringTest extends TestCase
{
    private string $command = 'app:reverse-string';

    public function test_reverse_string_with_passed_argument(): void
    {
        $string = 'привет';
        $reversedString = 'тевирп';

        $this
            ->artisan($this->command, [
                'string:string' => $string,
            ])
            ->assertSuccessful()
            ->expectsOutput($reversedString);
    }

    public function test_reverse_string_without_passed_argument(): void
    {
        $this->expectException(RuntimeException::class);

        $this->artisan($this->command);
    }
}
