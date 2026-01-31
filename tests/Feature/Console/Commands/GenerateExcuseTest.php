<?php

namespace Tests\Feature\Console\Commands;

use Exception;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

final class GenerateExcuseTest extends TestCase
{
    private const COMMAND = 'app:generate-excuse';

    public function test_generate_excuse_success(): void
    {
        $this->artisan(self::COMMAND)->assertSuccessful();
    }

    public function test_generate_excuse_failed_when_json_is_invalid(): void
    {
        File::shouldReceive('get')
            ->once()
            ->andReturn('{invalid json');

        $this->artisan(self::COMMAND)
            ->expectsOutputToContain('Syntax error')
            ->assertFailed();
    }

    public function test_generate_excuse_failed_when_file_does_not_exist(): void
    {
        File::shouldReceive('get')
            ->once()
            ->andThrow(new Exception('File does not exist'));

        $this->artisan(self::COMMAND)
            ->expectsOutputToContain('File does not exist')
            ->assertFailed();
    }
}
