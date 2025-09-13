<?php

namespace App\Listeners;

use App\Events\Search\UsersSearchIndexFilledEvent;
use App\Jobs\SendUsersSearchIndexDataJob;

class UsersSearchIndexFilledListener
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
