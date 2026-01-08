<?php

namespace Tests\Feature\Console\Commands\Elasticsearch;

use App\Clients\Elasticsearch\Contracts\ElasticsearchClientContract;
use App\Clients\Elasticsearch\ElasticsearchClientErrorStub;
use App\Clients\Elasticsearch\Exceptions\ElasticsearchApiException;
use App\Events\Search\UsersSearchIndexFilledEvent;
use App\Jobs\SendUsersSearchIndexDataJob;
use App\Listeners\NotifyAboutSearchIndexFilledListener;
use App\Mail\UsersSearchIndexDataMail;
use App\Models\Contracts\SearchableContract;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Mail\Mailer;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\DataProvider;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionException;
use Tests\Feature\Console\Commands\Elasticsearch\Abstract\SearchIndexCommandTest;

class FillSearchIndexTest extends SearchIndexCommandTest
{
    use RefreshDatabase;

    private const COMMAND = 'app:elasticsearch:fill-index';

    private NotifyAboutSearchIndexFilledListener $listener;

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
    }

    #[DataProvider('indexNameProvider')]
    public function test_fill_search_index_success(string $indexName): void
    {
        /** @var SearchableContract $modelName */
        $modelName = config('elasticsearch.search_index_models.'.$indexName);

        $count = 2;
        $models = $modelName::factory()->count($count)->withContact()->create();

        $expectedRows = $models->map(static fn (SearchableContract $model): array => [
            $model->id,
            --$model->id,
            '_doc',
            1,
            'created',
            1,
            201,
        ]);

        $this->executeCommand(['index_name' => $indexName])
            ->assertSuccessful()
            ->expectsTable(
                ['_id', '_seq_no', '_type', '_version', 'result', '_primary_term', 'status'],
                $expectedRows
            )
            ->expectsOutput(sprintf('index: %s', $indexName))
            ->expectsOutputToContain('took')
            ->expectsOutput('errors: false')
            ->expectsOutput(sprintf('created: %d', $count))
            ->expectsOutput(sprintf('updated: %d', 0))
            ->expectsOutput(sprintf('total: %d', $count));

        $events = Event::dispatched(UsersSearchIndexFilledEvent::class);
        $this->assertCount(1, $events);

        $event = $events->first()[0];
        $this->assertNotNull($event);
        $this->assertSame($indexName, $event->indexName);
        $this->assertCount($models->count(), $event->users);

        $this->listener->handle($event);

        /** @var SendUsersSearchIndexDataJob $job */
        $jobs = Queue::pushed(SendUsersSearchIndexDataJob::class);
        $this->assertCount(1, $jobs);

        $job = $jobs->first();
        $this->assertNotNull($job);
        $this->assertSame($indexName, $job->indexName);
        $this->assertCount($models->count(), $job->users);

        $job->handle(app(Mailer::class));

        $sentMails = Mail::sent(UsersSearchIndexDataMail::class);
        $this->assertCount(1, $sentMails);

        /** @var UsersSearchIndexDataMail $mail */
        $mail = $sentMails->first();
        $this->assertNotNull($mail);
        $this->assertTrue($mail->hasTo('admin@test.ru'));
        $this->assertSame($indexName, $mail->indexName);
        $this->assertSame($models->count(), $mail->usersCount);
    }

    #[DataProvider('indexNameProvider')]
    public function test_fill_search_index_with_argument_limit(string $indexName): void
    {
        /** @var SearchableContract $modelName */
        $modelName = config('elasticsearch.search_index_models.'.$indexName);

        $modelName::factory()->count(3)->withContact()->create();
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

        // TODO kpstya избавиться от эвента для users

        Event::assertNotDispatched(UsersSearchIndexFilledEvent::class);
    }

    /**
     * @throws ReflectionException
     */
    #[DataProvider('indexNameProvider')]
    public function test_fill_search_index_failed(string $indexName): void
    {
        $this->app->bind(ElasticsearchClientContract::class, static function (): ElasticsearchClientContract {
            return new ElasticsearchClientErrorStub;
        });

        /** @var SearchableContract $modelName */
        $modelName = config('elasticsearch.search_index_models.'.$indexName);

        $modelName::factory()->count(2)->withContact()->create();

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
