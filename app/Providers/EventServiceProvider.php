<?php

namespace App\Providers;

use App\Events\Search\UsersSearchIndexFilledEvent;
use App\Listeners\UsersSearchIndexFilledListener;
use Carbon\Laravel\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected array $listen = [
        UsersSearchIndexFilledEvent::class => [
            UsersSearchIndexFilledListener::class,
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
