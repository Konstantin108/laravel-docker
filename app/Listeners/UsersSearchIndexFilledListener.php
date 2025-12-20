<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\Search\UsersSearchIndexFilledEvent;
use App\Jobs\SendUsersSearchIndexDataJob;

// TODO kpstya сделать __invoke везде вместо handle() в командах и слушателях, и сделать слушатель readonly если позволит контракт

// TODO kpstya решить проблему со stan

// TODO kpstya переименовать слушатель

final class UsersSearchIndexFilledListener
{
    public function handle(UsersSearchIndexFilledEvent $event): void
    {
        SendUsersSearchIndexDataJob::dispatchIf(
            config('elasticsearch.send_report_to_email'),
            $event->users,
            $event->indexName
        );
    }
}
