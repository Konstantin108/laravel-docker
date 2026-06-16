<?php

declare(strict_types=1);

namespace App\Listeners;

// TODO kpstya в кастомных правилах для stan надо добавить типизацию

use App\Events\Elasticsearch\SearchIndexFilledEvent;
use App\Jobs\SendSearchIndexDataJob;

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
