<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\Excuse\GenerateExcuseAction;
use Illuminate\Console\Command;
use Throwable;

final class GenerateExcuseCommand extends Command
{
    protected $signature = 'app:generate-excuse';

    protected $description = 'Сгенерировать шаблонный ответ/отмазку для выступления на митапе';

    public function handle(GenerateExcuseAction $action): int
    {
        try {
            $excuse = $action->handle();
        } catch (Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->info($excuse);

        return self::SUCCESS;
    }
}
