<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Throwable;

final class GenerateExcuseCommand extends Command
{
    private const PATH = '/data/phrases.json';

    protected $signature = 'app:generate-excuse';

    protected $description = 'Сгенерировать шаблонный ответ/отмазку для выступления на митапе';

    public function handle(): int
    {
        try {
            $content = File::get(resource_path(self::PATH));
            $phrases = json_decode($content, true, flags: JSON_THROW_ON_ERROR);

            $excuse = sprintf(
                'Коллеги, %s вчера %s а сегодня %s на этом всё, %s',
                $phrases['greeting'][array_rand($phrases['greeting'])],
                $phrases['yesterday'][array_rand($phrases['yesterday'])],
                $phrases['today'][array_rand($phrases['today'])],
                $phrases['ending'][array_rand($phrases['ending'])],
            );

        } catch (Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->info($excuse);

        return self::SUCCESS;
    }
}
