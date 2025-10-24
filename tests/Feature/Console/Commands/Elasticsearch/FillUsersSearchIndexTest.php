<?php

namespace Tests\Feature\Console\Commands\Elasticsearch;

use App\Clients\Elasticsearch\Contracts\ElasticsearchClientContract;
use App\Clients\Elasticsearch\ElasticsearchClientErrorStub;
use App\Clients\Elasticsearch\ElasticsearchClientStub;
use App\Events\Search\UsersSearchIndexFilledEvent;
use App\Exceptions\ElasticsearchApiException;
use App\Jobs\SendUsersSearchIndexDataJob;
use App\Listeners\UsersSearchIndexFilledListener;
use App\Mail\UsersSearchIndexDataMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Mail\Mailer;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionException;
use Tests\TestCase;

class FillUsersSearchIndexTest extends TestCase
{
    use RefreshDatabase;

    private const COMMAND = 'app:search:fill-users-search-index';

    private UsersSearchIndexFilledListener $listener;

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
        $this->listener = $this->app->get(UsersSearchIndexFilledListener::class);
    }

    /**
     * @throws ReflectionException
     */
    public function test_fill_users_search_index_success(): void
    {
        $this->app->bind(ElasticsearchClientContract::class, static function () {
            return new ElasticsearchClientStub;
        });

        $indexName = 'users';
        $count = 2;
        $users = User::factory()->count($count)->withContact()->create();

        $expectedRows = $users
            ->map(static fn (User $user): array => [
                $user->id,
                --$user->id,
                '_doc',
                1,
                'created',
                1,
                201,
            ]);

        $this
            ->artisan(self::COMMAND)
            ->assertSuccessful()
            ->expectsTable(
                ['_id', '_seq_no', '_type', '_version', 'result', '_primary_term', 'status'],
                $expectedRows
            )
            ->expectsOutputToContain('took')
            ->expectsOutput(sprintf('index: %s', $indexName))
            ->expectsOutput('errors: false')
            ->expectsOutput(sprintf('created: %d', $count))
            ->expectsOutput(sprintf('updated: %d', 0))
            ->expectsOutput(sprintf('total: %d', $count))
            ->doesntExpectOutput('index: contacts')
            ->doesntExpectOutput('errors: true')
            ->doesntExpectOutput(sprintf('created: %d', 0))
            ->doesntExpectOutput(sprintf('updated: %d', $count))
            ->doesntExpectOutput(sprintf('total: %d', 0));

        $event = Event::dispatched(UsersSearchIndexFilledEvent::class)
            ->first()[0];
        $this->assertNotNull($event);

        Event::assertDispatched($event::class, static function (UsersSearchIndexFilledEvent $event) use ($users, $indexName): bool {
            return $event->users->count() === $users->count()
                && $event->indexName === $indexName;
        });

        $this->listener->handle($event);

        /** @var SendUsersSearchIndexDataJob $job */
        $job = Queue::pushed(SendUsersSearchIndexDataJob::class)
            ->first();
        $this->assertNotNull($job);

        Queue::assertPushed($job::class, static function (SendUsersSearchIndexDataJob $job) use ($users, $indexName): bool {
            return $job->users->count() === $users->count()
                && $job->indexName === $indexName;
        });

        $job->handle(app(Mailer::class));

        Mail::assertSent(
            UsersSearchIndexDataMail::class,
            static function (UsersSearchIndexDataMail $mail) use ($users, $indexName): bool {
                return $mail->usersCount === $users->count()
                    && $mail->indexName === $indexName;
            }
        );
    }

    /**
     * @throws ReflectionException
     */
    public function test_fill_users_search_index_when_users_table_is_empty(): void
    {
        $this->app->bind(ElasticsearchClientContract::class, static function () {
            return new ElasticsearchClientStub;
        });

        $this
            ->artisan(self::COMMAND)
            ->assertSuccessful()
            ->expectsOutput('null');

        Event::assertNotDispatched(UsersSearchIndexFilledEvent::class);
    }

    /**
     * @throws ReflectionException
     */
    public function test_fill_users_search_index_failed(): void
    {
        $this->app->bind(ElasticsearchClientContract::class, static function () {
            return new ElasticsearchClientErrorStub;
        });

        User::factory()->count(2)->withContact()->create();

        $this->expectException(ElasticsearchApiException::class);
        $this->expectExceptionMessage('Index filling error');

        $this->artisan(self::COMMAND);
    }
}
