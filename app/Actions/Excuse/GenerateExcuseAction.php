<?php

declare(strict_types=1);

namespace App\Actions\Excuse;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use JsonException;

final class GenerateExcuseAction
{
    private const PATH = '/data/phrases.json';

    public function __construct(
        private readonly Filesystem $filesystem,
    ) {}

    // TODO kpstya исправить stan комментарии

    // TODO kpsaty тест для рандома

    // TODO kpstya в тесте фасад это нормально?

    /**
     * @throws FileNotFoundException
     * @throws JsonException
     */
    public function handle(): string
    {
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
