<?php

declare(strict_types=1);

namespace Tests\Feature\Console\Commands;

use Symfony\Component\Console\Exception\RuntimeException;
use Tests\TestCase;

class ReverseStringTest extends TestCase
{
    private string $command = 'app:reverse-string';

    public function test_reverse_string(): void
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

    public function test_reverse_string_failed(): void
    {
        $this->expectException(RuntimeException::class);

        $this
            ->artisan($this->command)
            ->assertFailed()
            ->expectsOutput('');
    }
}
