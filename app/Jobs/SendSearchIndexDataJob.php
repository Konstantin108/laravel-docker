<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Mail\SearchIndexDataMail;
use App\Services\Contracts\SearchableSourceContract;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;

final class SendSearchIndexDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public int $tries = 3;

    public function __construct(
        /** @var Collection<int, SearchableSourceContract> */
        public readonly Collection $items,
        public readonly string $indexName
    ) {}

    public function handle(Mailer $mailer): void
    {
        $mailer->to(config('mail.admin_email_address'))
            ->send(new SearchIndexDataMail(
                $this->items,
                $this->items->count(),
                $this->indexName
            ));
    }
}
