<?php

namespace Tests\Unit\Listeners;

use App\Events\Elasticsearch\SearchIndexFilledEvent;
use App\Jobs\SendSearchIndexDataJob;
use App\Listeners\NotifyAboutSearchIndexFilledListener;
use App\Services\Contracts\SearchableSourceContract;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class NotifyAboutSearchIndexFilledListenerTest extends TestCase
{
    #[Test]
    public function it_dispatches_job_when_report_is_enabled(): void
    {
        Bus::fake();
        $indexName = 'any_index_name';
        $items = new Collection($this->mock(SearchableSourceContract::class));
        $event = new SearchIndexFilledEvent($items, $indexName);

        (new NotifyAboutSearchIndexFilledListener)->handle($event);

        /* TODO kpstya
            - возможно избавиться от хелперов включая config()
            - возможно избавиться от использования фасадов */

        $jobs = Bus::dispatched(SendSearchIndexDataJob::class);
        $this->assertCount(1, $jobs);

        $job = $jobs->first();
        $this->assertNotNull($job);
        $this->assertSame($indexName, $job->indexName);
        $this->assertSame($items, $job->items);
        $this->assertCount($items->count(), $job->items);
    }

    #[Test]
    public function it_does_not_dispatch_job_when_report_is_disabled(): void
    {
        config()->set('elasticsearch.send_report_to_email', false);

        Bus::fake();
        $event = new SearchIndexFilledEvent(
            new Collection($this->mock(SearchableSourceContract::class)),
            'any_index'
        );

        (new NotifyAboutSearchIndexFilledListener)->handle($event);

        Bus::assertNotDispatched(SendSearchIndexDataJob::class);
    }
}
