<?php

namespace Tests\Feature\Console\Commands\Elasticsearch;

use App\Clients\Elasticsearch\Contracts\ElasticsearchClientContract;
use App\Clients\Elasticsearch\ElasticsearchClientErrorStub;
use App\Clients\Elasticsearch\Exceptions\ElasticsearchApiException;
use App\Events\Search\UsersSearchIndexFilledEvent;
use App\Jobs\SendUsersSearchIndexDataJob;
use App\Listeners\NotifyAboutSearchIndexFilledListener;
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

    public function test_fill_users_search_index_success(): void
    {
        $indexName = 'users';
        $count = 2;
        $users = User::factory()->count($count)->withContact()->create();

        $expectedRows = $users->map(static fn (User $user): array => [
            $user->id,
            --$user->id,
            '_doc',
            1,
            'created',
            1,
            201,
        ]);

        $this->artisan(self::COMMAND)
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
        $this->assertSame($users->count(), $event->users->count());

        $this->listener->handle($event);

        /** @var SendUsersSearchIndexDataJob $job */
        $jobs = Queue::pushed(SendUsersSearchIndexDataJob::class);
        $this->assertCount(1, $jobs);

        $job = $jobs->first();
        $this->assertNotNull($job);
        $this->assertSame($indexName, $job->indexName);
        $this->assertSame($users->count(), $job->users->count());

        $job->handle(app(Mailer::class));

        $sentMails = Mail::sent(UsersSearchIndexDataMail::class);
        $this->assertCount(1, $sentMails);

        /** @var UsersSearchIndexDataMail $mail */
        $mail = $sentMails->first();
        $this->assertNotNull($mail);
        $this->assertTrue($mail->hasTo('kv.dryakhlov@bgit.ru'));
        $this->assertSame($indexName, $mail->indexName);
        $this->assertSame($users->count(), $mail->usersCount);
    }

    public function test_fill_users_search_index_with_argument_limit(): void
    {
        User::factory()->count(3)->withContact()->create();
        $limit = 2;

        $this->artisan(self::COMMAND, [
            'limit:int' => $limit,
        ])
            ->assertSuccessful()
            ->expectsOutput(sprintf('total: %d', $limit));
    }

    public function test_fill_users_search_index_when_users_table_is_empty(): void
    {
        $this->artisan(self::COMMAND)
            ->assertSuccessful()
            ->expectsOutput('null');

        Event::assertNotDispatched(UsersSearchIndexFilledEvent::class);
    }

    /**
     * @throws ReflectionException
     */
    public function test_fill_users_search_index_failed(): void
    {
        $this->app->bind(ElasticsearchClientContract::class, static function (): ElasticsearchClientContract {
            return new ElasticsearchClientErrorStub;
        });

        User::factory()->count(2)->withContact()->create();

        $this->expectException(ElasticsearchApiException::class);
        $this->expectExceptionMessage('Index filling error.');

        $this->artisan(self::COMMAND);
    }
}
