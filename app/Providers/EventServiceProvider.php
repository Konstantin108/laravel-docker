<?php

declare(strict_types=1);

namespace App\Providers;

use App\Events\Elasticsearch\SearchIndexFilledEvent;
use App\Listeners\NotifyAboutSearchIndexFilledListener;
use Carbon\Laravel\ServiceProvider;

final class EventServiceProvider extends ServiceProvider
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
