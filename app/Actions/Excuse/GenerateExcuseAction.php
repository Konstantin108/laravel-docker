<?php

declare(strict_types=1);

namespace App\Actions\Excuse;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use JsonException;

final readonly class GenerateExcuseAction
{
    private const PATH = '/data/phrases.json';

    public function __construct(private Filesystem $filesystem) {}

    /**
     * @throws FileNotFoundException
     * @throws JsonException
     */
    public function handle(): string
    {
        // TODO kpstya resource_path - можно ли заменить это на фасад или же DI

        $content = $this->filesystem->get(resource_path(self::PATH));
        $phrases = json_decode($content, true, flags: JSON_THROW_ON_ERROR);

        return sprintf(
            'Коллеги, %s вчера %s а сегодня %s на этом всё, %s',
            $this->getRandomPhrase($phrases['greeting']),
            $this->getRandomPhrase($phrases['yesterday']),
            $this->getRandomPhrase($phrases['today']),
            $this->getRandomPhrase($phrases['ending']),
        );
    }

    /**
     * @param  list<string>  $data
     */
    private function getRandomPhrase(array $data): string
    {
        return $data[array_rand($data)];
    }
}
