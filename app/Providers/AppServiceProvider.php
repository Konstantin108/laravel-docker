<?php

namespace App\Providers;

use App\Clients\Elasticsearch\Contracts\ElasticsearchClientContract;
use App\Clients\Elasticsearch\Dto\SettingsDto;
use App\Clients\Elasticsearch\ElasticsearchClient;
use App\Clients\Elasticsearch\ElasticsearchClientStub;
use App\Factories\Contracts\SourceDtoFactoryContract;
use App\Http\Controllers\Api\v2\ProductController;
use App\Http\Controllers\Api\v2\UserController;
use App\Repositories\Product\Contracts\ProductRepositoryContract;
use App\Repositories\Product\ProductEloquentRepository;
use App\Repositories\User\Contracts\UserRepositoryContract;
use App\Repositories\User\UserEloquentRepository;
use App\Services\Elasticsearch\Factories\ElasticsearchRepositoryFactory;
use App\Services\Elasticsearch\Repositories\Contracts\ElasticsearchRepositoryContract;
use App\Services\Elasticsearch\Repositories\ProductElasticsearchRepository;
use App\Services\Elasticsearch\Repositories\UserElasticsearchRepository;
use App\Services\Elasticsearch\SourceDtoCollectionService;
use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Dedoc\Scramble\ScrambleServiceProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use RuntimeException;

final class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->environment('local')) {
            $this->app->register(IdeHelperServiceProvider::class);
        }

        if ($this->app->environment('local') || config('scramble.enabled')) {
            $this->app->register(ScrambleServiceProvider::class);
        }

        $this->app->bind(UserRepositoryContract::class, UserEloquentRepository::class);
        $this->app->bind(ProductRepositoryContract::class, ProductEloquentRepository::class);

        $this->app->singleton(ElasticsearchClientContract::class, static function (Application $app): ElasticsearchClientContract {
            if ($app->environment('testing')) {
                return $app->make(ElasticsearchClientStub::class);
            }

            return new ElasticsearchClient(
                config('elasticsearch.url'),
                config('elasticsearch.user'),
                config('elasticsearch.password'),
                SettingsDto::from(config('elasticsearch.settings')),
            );
        });

        $this->app->singleton(SourceDtoCollectionService::class, static function (Application $app): SourceDtoCollectionService {
            return new SourceDtoCollectionService(...array_map(
                static fn (string $className): SourceDtoFactoryContract => $app->make($className),
                config('elasticsearch.source_dto_factories')
            ));
        });

        $this->app->singleton(ElasticsearchRepositoryFactory::class, static function (Application $app): ElasticsearchRepositoryFactory {
            return new ElasticsearchRepositoryFactory(...array_map(
                static fn (string $className): ElasticsearchRepositoryContract => $app->make($className),
                config('elasticsearch.repositories')
            ));
        });

        $this->app->when(UserController::class)
            ->needs(ElasticsearchRepositoryContract::class)
            ->give(UserElasticsearchRepository::class);

        $this->app->when(ProductController::class)
            ->needs(ElasticsearchRepositoryContract::class)
            ->give(ProductElasticsearchRepository::class);
    }

    public function boot(): void
    {
        if ($this->app->environment('production')) {
            DB::prohibitDestructiveCommands();

            if (config('app.debug')) {
                throw new RuntimeException('Debug must be disabled in production!');
            }
        }

        Model::shouldBeStrict();

        if ($this->app->environment('local') && config('app.query_log')) {
            DB::listen(static function (QueryExecuted $query): void {
                Log::channel('query_log')->debug($query->sql, [
                    'bindings' => $query->bindings,
                    'time_ms' => $query->time,
                ]);
            });
        }
    }
}
