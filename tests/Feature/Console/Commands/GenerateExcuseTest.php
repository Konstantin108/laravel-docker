<?php

namespace Tests\Feature\Console\Commands;

use Exception;
use Illuminate\Filesystem\Filesystem;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class GenerateExcuseTest extends TestCase
{
    private const COMMAND = 'app:generate-excuse';

    private const PATH = '/data/phrases.json';

    private Filesystem $filesystem;

    protected function setUp(): void
    {
        parent::setUp();

        $this->filesystem = Mockery::mock(Filesystem::class);
        $this->app->instance(Filesystem::class, $this->filesystem);
    }

    #[Test]
    public function it_successfully_generates_excuse(): void
    {
        $data = [
            'greeting' => ['привет,'],
            'yesterday' => ['писал код.'],
            'today' => ['чиню баги.'],
            'ending' => ['вопросы?'],
        ];

        $this->filesystem
            ->shouldReceive('get')
            ->once()
            ->with(resource_path(self::PATH))
            ->andReturn(json_encode($data));

        $this->artisan(self::COMMAND)
            ->expectsOutputToContain(sprintf(
                'Коллеги, %s вчера %s а сегодня %s на этом всё, %s',
                $data['greeting'][0],
                $data['yesterday'][0],
                $data['today'][0],
                $data['ending'][0],
            ))
            ->assertSuccessful();
    }

    #[Test]
    public function it_returns_error_when_generating_excuse_with_invalid_json(): void
    {
        $this->filesystem
            ->shouldReceive('get')
            ->once()
            ->with(resource_path(self::PATH))
            ->andReturn('{invalid json');

        $this->artisan(self::COMMAND)
            ->expectsOutputToContain('Syntax error')
            ->assertFailed();
    }

    #[Test]
    public function it_returns_error_when_generating_excuse_for_for_missing_file(): void
    {
        $this->filesystem
            ->shouldReceive('get')
            ->once()
            ->with(resource_path(self::PATH))
            ->andThrow(new Exception('File does not exist'));

        $this->artisan(self::COMMAND)
            ->expectsOutputToContain('File does not exist')
            ->assertFailed();
    }
}
