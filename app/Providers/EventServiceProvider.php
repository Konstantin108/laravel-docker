<?php

namespace App\Providers;

use App\Events\Elasticsearch\SearchIndexFilledEvent;
use App\Listeners\NotifyAboutSearchIndexFilledListener;
use Carbon\Laravel\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * @var array<class-string, array<int, class-string>>
     */
    protected array $listen = [
        SearchIndexFilledEvent::class => [
            NotifyAboutSearchIndexFilledListener::class,
        ],
    ];
}
