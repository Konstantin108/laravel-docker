<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\Elasticsearch\SearchIndexFilledEvent;
use App\Jobs\SendSearchIndexDataJob;

// TODO kpstya избавиться от dispatchIf()

final class NotifyAboutSearchIndexFilledListener
{
    public function handle(SearchIndexFilledEvent $event): void
    {
        SendSearchIndexDataJob::dispatchIf(
            config('elasticsearch.send_report_to_email'),
            $event->items,
            $event->indexName
        );
    }
}
