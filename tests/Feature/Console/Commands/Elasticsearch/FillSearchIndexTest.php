<?php

namespace Tests\Feature\Console\Commands\Elasticsearch;

use App\Clients\Elasticsearch\Contracts\ElasticsearchClientContract;
use App\Clients\Elasticsearch\ElasticsearchClientErrorStub;
use App\Clients\Elasticsearch\Exceptions\ElasticsearchApiException;
use App\Events\Elasticsearch\SearchIndexFilledEvent;
use App\Jobs\SendSearchIndexDataJob;
use App\Listeners\NotifyAboutSearchIndexFilledListener;
use App\Mail\SearchIndexDataMail;
use App\Models\Contracts\SearchableContract;
use App\Services\Elasticsearch\Enums\SearchIndexEnum;
use App\Services\Elasticsearch\Exceptions\SearchIndexException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Mail\Mailer;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Mockery\Expectation;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionException;
use Tests\Feature\Console\Commands\Elasticsearch\Abstract\SearchIndexCommandTest;

final class FillSearchIndexTest extends SearchIndexCommandTest
{
    use RefreshDatabase;

    private const COMMAND = 'app:elasticsearch:fill-index';

    private NotifyAboutSearchIndexFilledListener $listener;

    private MockInterface $logger;

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
        $this->logger = Log::spy();
    }

    /**
     * @throws SearchIndexException
     */
    #[DataProvider('indexNameProvider')]
    public function test_fill_search_index_success(string $indexName): void
    {
        $model = SearchIndexEnum::from($indexName)->getModel();

        $count = 2;
        $models = $model::factory()->count($count)->create();

        $expectedRows = $models->map(static fn (SearchableContract $model): array => [
            $model->id,
            --$model->id,
            $indexName,
            1,
            'created',
            1,
            201,
            '_doc',
        ]);

        $this->executeCommand(['index_name' => $indexName])
            ->assertSuccessful()
            ->expectsTable(
                ['_id', '_seq_no', '_index', '_version', 'result', '_primary_term', 'status', '_type'],
                $expectedRows
            )
            ->expectsOutput(sprintf('index: %s', $indexName))
            ->expectsOutputToContain('took')
            ->expectsOutput('errors: false')
            ->expectsOutput(sprintf('created: %d', $count))
            ->expectsOutput(sprintf('updated: %d', 0))
            ->expectsOutput(sprintf('total: %d', $count));

        $events = Event::dispatched(SearchIndexFilledEvent::class);
        $this->assertCount(1, $events);

        $event = $events->first()[0];
        $this->assertNotNull($event);
        $this->assertSame($indexName, $event->indexName);
        $this->assertCount($models->count(), $event->items);

        $this->listener->handle($event);

        /** @var SendSearchIndexDataJob $job */
        $jobs = Queue::pushed(SendSearchIndexDataJob::class);
        $this->assertCount(1, $jobs);

        $job = $jobs->first();
        $this->assertNotNull($job);
        $this->assertSame($indexName, $job->indexName);
        $this->assertCount($models->count(), $job->items);

        $job->handle(app(Mailer::class));

        $sentMails = Mail::sent(SearchIndexDataMail::class);
        $this->assertCount(1, $sentMails);

        /** @var SearchIndexDataMail $mail */
        $mail = $sentMails->first();
        $this->assertNotNull($mail);
        $this->assertTrue($mail->hasTo('admin@test.ru'));
        $this->assertSame($indexName, $mail->indexName);
        $this->assertSame($models->count(), $mail->itemsCount);
    }

    #[DataProvider('indexNameProvider')]
    public function test_fill_index_log_disabled(string $indexName): void
    {
        $this->executeCommand(['index_name' => $indexName]);
        $this->logger->shouldNotHaveReceived('info');
    }

    #[DataProvider('indexNameProvider')]
    public function test_fill_index_log_enabled(string $indexName): void
    {
        config()->set('elasticsearch.fill_index_log', true);

        $this->executeCommand(['index_name' => $indexName]);

        /** @var Expectation $expectation */
        $expectation = $this->logger->shouldHaveReceived('info');
        $expectation->once();
    }

    /**
     * @throws SearchIndexException
     */
    #[DataProvider('indexNameProvider')]
    public function test_fill_search_index_with_argument_limit(string $indexName): void
    {
        $model = SearchIndexEnum::from($indexName)->getModel();
        $model::factory()->count(3)->create();
        $limit = 2;

        $this->artisan(self::COMMAND, [
            'index_name' => $indexName,
            '--limit' => $limit,
        ])
            ->assertSuccessful()
            ->expectsOutput(sprintf('total: %d', $limit));
    }

    #[DataProvider('indexNameProvider')]
    public function test_fill_search_index_when_table_is_empty(string $indexName): void
    {
        $this->executeCommand(['index_name' => $indexName])
            ->assertSuccessful()
            ->expectsOutput('null');

        Event::assertNotDispatched(SearchIndexFilledEvent::class);
    }

    /**
     * @throws ReflectionException
     * @throws SearchIndexException
     */
    #[DataProvider('indexNameProvider')]
    public function test_fill_search_index_failed(string $indexName): void
    {
        $this->app->bind(ElasticsearchClientContract::class, static function (): ElasticsearchClientContract {
            return new ElasticsearchClientErrorStub;
        });

        $model = SearchIndexEnum::from($indexName)->getModel();
        $model::factory()->count(2)->create();

        $this->expectException(ElasticsearchApiException::class);
        $this->expectExceptionMessage('Index filling error.');

        $this->executeCommand(['index_name' => $indexName]);
    }

    public function test_invalid_search_index_name(): void
    {
        $this->exceptInvalidSearchIndexName('usdrs');
    }

    #[DataProvider('indexNameProvider')]
    public function test_expects_questions(string $indexName): void
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
