<?php

namespace Tests\Feature\Console\Commands\Elasticsearch;

use App\Clients\Elasticsearch\Exceptions\ElasticsearchApiException;
use App\Events\Elasticsearch\SearchIndexFilledEvent;
use App\Jobs\SendSearchIndexDataJob;
use App\Listeners\NotifyAboutSearchIndexFilledListener;
use App\Mail\SearchIndexDataMail;
use App\Models\Contracts\SearchableContract;
use App\Services\Elasticsearch\Enums\SearchIndexEnum;
use App\Services\Elasticsearch\Exceptions\SearchIndexException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Mail\Mailer;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerInterface;
use Tests\TestCases\SearchIndexCommandTestCase;

final class FillSearchIndexCommandTest extends SearchIndexCommandTestCase
{
    use RefreshDatabase;

    private const COMMAND = 'app:elasticsearch:fill-index';

    private NotifyAboutSearchIndexFilledListener $listener;

    private LoggerInterface $logger;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function setUp(): void
    {
        parent::setUp();

        Event::fake();
        Queue::fake();
        Mail::fake();
        $this->listener = $this->app->get(NotifyAboutSearchIndexFilledListener::class);

        /** @var LoggerInterface&MockInterface $logger */
        $logger = $this->mock(LoggerInterface::class);
        $this->logger = $logger;
    }

    /**
     * @throws SearchIndexException
     * @throws BindingResolutionException
     */
    #[Test]
    #[DataProvider(methodName: 'indexNameProvider')]
    public function it_successfully_fills_search_index(string $indexName): void
    {
        $model = SearchIndexEnum::from($indexName)->getModel();

        $count = 2;
        $models = $model::factory()->count($count)->create();

        $expectedRows = $models->map(static fn (SearchableContract $model): array => [
            $model->id,
            $model->id - 1,
            $indexName,
            1,
            'created',
            1,
            201,
            '_doc',
        ]);

        $this->executeCommand(['index_name' => $indexName])
            ->expectsTable(
                ['_id', '_seq_no', '_index', '_version', 'result', '_primary_term', 'status', '_type'],
                $expectedRows
            )
            ->expectsOutput(sprintf('index: %s', $indexName))
            ->expectsOutputToContain('took')
            ->expectsOutput('errors: false')
            ->expectsOutput(sprintf('created: %d', $count))
            ->expectsOutput(sprintf('updated: %d', 0))
            ->expectsOutput(sprintf('total: %d', $count))
            ->assertSuccessful();

        $events = Event::dispatched(SearchIndexFilledEvent::class);
        $this->assertCount(1, $events);

        $event = $events->first()[0];
        $this->assertNotNull($event);
        $this->assertSame($indexName, $event->indexName);
        $this->assertCount($models->count(), $event->items);

        $this->listener->handle($event);

        // TODO kpstya возможно избавиться от pushed()

        /** @var SendSearchIndexDataJob $job */
        $jobs = Queue::pushed(SendSearchIndexDataJob::class);
        $this->assertCount(1, $jobs);

        $job = $jobs->first();
        $this->assertNotNull($job);
        $this->assertSame($indexName, $job->indexName);
        $this->assertCount($models->count(), $job->items);

        $job->handle($this->app->make(Mailer::class));

        $sentMails = Mail::sent(SearchIndexDataMail::class);
        $this->assertCount(1, $sentMails);

        /** @var SearchIndexDataMail $mail */
        $mail = $sentMails->first();
        $this->assertNotNull($mail);
        $this->assertTrue($mail->hasTo('admin@test.ru'));
        $this->assertSame($indexName, $mail->indexName);
        $this->assertSame($models->count(), $mail->itemsCount);
    }

    #[Test]
    #[DataProvider(methodName: 'indexNameProvider')]
    public function it_does_not_record_info_log_when_filling_index_and_logging_is_disabled(string $indexName): void
    {
        $this->logger->shouldReceive('info')->never();
        $this->executeCommand(['index_name' => $indexName]);
    }

    #[Test]
    #[DataProvider(methodName: 'indexNameProvider')]
    public function it_records_info_log_when_filling_index_and_logging_is_enabled(string $indexName): void
    {
        config()->set('elasticsearch.fill_index_log', true);

        $this->logger->shouldReceive('info')->once();
        $this->executeCommand(['index_name' => $indexName]);
    }

    /**
     * @throws SearchIndexException
     */
    #[Test]
    #[DataProvider(methodName: 'indexNameProvider')]
    public function it_fills_search_index_with_argument_limit(string $indexName): void
    {
        $model = SearchIndexEnum::from($indexName)->getModel();
        $model::factory()->count(3)->create();
        $limit = 2;

        $this->artisan(self::COMMAND, [
            'index_name' => $indexName,
            '--limit' => $limit,
        ])
            ->expectsOutput(sprintf('total: %d', $limit))
            ->assertSuccessful();
    }

    #[Test]
    #[DataProvider(methodName: 'indexNameProvider')]
    public function it_fills_search_index_when_table_is_empty(string $indexName): void
    {
        $this->executeCommand(['index_name' => $indexName])
            ->expectsOutput('null')
            ->assertSuccessful();

        Event::assertNotDispatched(SearchIndexFilledEvent::class);
    }

    /**
     * @throws SearchIndexException
     */
    #[Test]
    #[DataProvider(methodName: 'indexNameProvider')]
    public function it_returns_error_when_filling_search_index_fails(string $indexName): void
    {
        $model = SearchIndexEnum::from($indexName)->getModel();
        $model::factory()->count(2)->create();
        $exceptionMessage = 'Index filling error.';

        $this->callMethodWithException('bulkIndex', $exceptionMessage);

        $this->expectException(ElasticsearchApiException::class);
        $this->expectExceptionMessage($exceptionMessage);

        $this->executeCommand(['index_name' => $indexName]);
    }

    #[Test]
    public function it_returns_error_when_invalid_search_index_name_is_given(): void
    {
        $this->exceptInvalidSearchIndexName('usdrs');
    }

    #[Test]
    #[DataProvider(methodName: 'indexNameProvider')]
    public function it_returns_questions_for_given_index(string $indexName): void
    {
        $this->expectsPrompts($indexName)
            ->expectsQuestion('Указать лимит отправялемых записей?', '')
            ->assertSuccessful();
    }

    protected function command(): string
    {
        return self::COMMAND;
    }
}
