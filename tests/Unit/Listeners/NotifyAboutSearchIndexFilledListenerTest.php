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
            - надо получать в тестах экземпяр $job и ассертить
            - возможно вынести дублирование в setUp()
            - возможно избавиться от хелперов включая config()
            - возможно избавиться от использования фасадов */

        Bus::assertDispatched(
            SendSearchIndexDataJob::class,
            static function (SendSearchIndexDataJob $job) use ($items, $indexName): bool {
                return $job->items === $items && $job->indexName === $indexName;
            }
        );
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
