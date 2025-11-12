<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

final class ReverseStringCommand extends Command
{
    protected $signature = 'app:reverse-string {string:string}';

    protected $description = 'Развернуть строку';

    public function handle(): int
    {
        $this->info(Str::reverse($this->argument('string:string')));

        return self::SUCCESS;
    }
}
