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
    private const INDEX_NAME = 'any_index_name';

    protected function setUp(): void
    {
        parent::setUp();

        Bus::fake();
    }

    #[Test]
    public function it_dispatches_job_when_report_is_enabled(): void
    {
        $items = new Collection($this->mock(SearchableSourceContract::class));
        $event = new SearchIndexFilledEvent($items, self::INDEX_NAME);

        (new NotifyAboutSearchIndexFilledListener)->handle($event);

        /* TODO kpstya
            - возможно избавиться от хелперов включая config()
            - возможно избавиться от использования фасадов */

        $jobs = Bus::dispatched(SendSearchIndexDataJob::class);
        $this->assertCount(1, $jobs);

        $job = $jobs->first();
        $this->assertNotNull($job);
        $this->assertSame(self::INDEX_NAME, $job->indexName);
        $this->assertSame($items, $job->items);
        $this->assertCount($items->count(), $job->items);
    }

    #[Test]
    public function it_does_not_dispatch_job_when_report_is_disabled(): void
    {
        config()->set('elasticsearch.send_report_to_email', false);

        $event = new SearchIndexFilledEvent(
            new Collection($this->mock(SearchableSourceContract::class)),
            self::INDEX_NAME
        );

        (new NotifyAboutSearchIndexFilledListener)->handle($event);

        Bus::assertNotDispatched(SendSearchIndexDataJob::class);
    }
}
