<?php

namespace App\Providers;

use App\Events\Elasticsearch\SearchIndexFilledEvent;
use App\Listeners\NotifyAboutSearchIndexFilledListener;
use Carbon\Laravel\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected array $listen = [
        SearchIndexFilledEvent::class => [
            NotifyAboutSearchIndexFilledListener::class,
        ],
    ];

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
