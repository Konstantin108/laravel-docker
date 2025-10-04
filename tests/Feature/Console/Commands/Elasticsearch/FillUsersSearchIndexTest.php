<?php

namespace Tests\Feature\Console\Commands\Elasticsearch;

use App\Clients\Elasticsearch\Contracts\ElasticsearchClientContract;
use App\Clients\Elasticsearch\ElasticsearchClientErrorStub;
use App\Clients\Elasticsearch\ElasticsearchClientStub;
use App\Events\Search\UsersSearchIndexFilledEvent;
use App\Exceptions\ElasticsearchApiException;
use App\Jobs\SendUsersSearchIndexDataJob;
use App\Listeners\UsersSearchIndexFilledListener;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Tests\TestCase;

class FillUsersSearchIndexTest extends TestCase
{
    use RefreshDatabase;

    private string $command = 'app:search:fill-users-search-index';

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
        $this->listener = $this->app->get(UsersSearchIndexFilledListener::class);
    }

    /**
     * @throws \ReflectionException
     */
    public function test_fill_users_search_index_success(): void
    {
        $this->app->bind(ElasticsearchClientContract::class, static function () {
            return new ElasticsearchClientStub;
        });

        $indexName = 'users';
        $users = User::factory()->count(2)->withContact()->create();

        $this
            ->artisan($this->command)
            ->assertSuccessful()
            ->expectsOutputToContain(sprintf(
                '"_id":"%d"',
                $users->first()->id
            ));

        $dispatchedEvents = Event::dispatched(UsersSearchIndexFilledEvent::class);
        $event = $dispatchedEvents->first()[0];

        $this->assertNotNull($event);

        Event::assertDispatched(
            $event::class,
            static function (UsersSearchIndexFilledEvent $event) use ($users, $indexName) {
                return $event->users->count() === $users->count()
                    && $event->indexName === $indexName;
            }
        );

        $this->listener->handle($event);

        Queue::assertPushed(
            SendUsersSearchIndexDataJob::class,
            static function (SendUsersSearchIndexDataJob $job) use ($users, $indexName) {
                return $job->users->count() === $users->count()
                    && $job->indexName === $indexName;
            }
        );
    }

    /**
     * @throws \ReflectionException
     */
    public function test_fill_users_search_index_when_users_table_is_empty(): void
    {
        $this->app->bind(ElasticsearchClientContract::class, static function () {
            return new ElasticsearchClientStub;
        });

        $this
            ->artisan($this->command)
            ->assertSuccessful()
            ->expectsOutputToContain('null');

        Event::assertNotDispatched(UsersSearchIndexFilledEvent::class);
    }

    /**
     * @throws \ReflectionException
     */
    public function test_fill_users_search_index_failed(): void
    {
        $this->app->bind(ElasticsearchClientContract::class, static function () {
            return new ElasticsearchClientErrorStub;
        });

        User::factory()->count(2)->withContact()->create();

        $this->expectException(ElasticsearchApiException::class);
        $this->expectExceptionMessage('Index filling error');

        $this
            ->artisan($this->command)
            ->assertFailed()
            ->expectsOutput('');
    }
}
