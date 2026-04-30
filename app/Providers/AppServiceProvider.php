<?php

namespace App\Providers;

/* TODO kpstya - возможно реализовывать интерфейс будет базовый абстрактный класс, а уже от него будут наследоваться
    другие классы. Тогда я смогу упростить и использовать в сервисах класс контракт и возможно смогу отвязать
    использование эвента для каждого сервиса, сейчас это не много костыльно и слишком высокий уровень каплинга */

use App\Clients\Elasticsearch\Contracts\ElasticsearchClientContract;
use App\Clients\Elasticsearch\Dto\SettingsDto;
use App\Clients\Elasticsearch\ElasticsearchClient;
use App\Clients\Elasticsearch\ElasticsearchClientStub;
use App\Factories\Contracts\SourceDtoFactoryContract;
use App\Repositories\Product\Contracts\ProductRepositoryContract;
use App\Repositories\Product\ProductEloquentRepository;
use App\Repositories\User\Contracts\UserRepositoryContract;
use App\Repositories\User\UserEloquentRepository;
use App\Services\Elasticsearch\Abstract\ElasticsearchService;
use App\Services\Elasticsearch\Factories\ElasticsearchServiceFactory;
use App\Services\Elasticsearch\SourceDtoCollectionService;
use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use RuntimeException;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->environment('local')) {
            $this->app->register(IdeHelperServiceProvider::class);
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

        $this->app->singleton(ElasticsearchServiceFactory::class, static function (Application $app): ElasticsearchServiceFactory {
            return new ElasticsearchServiceFactory(...array_map(
                static fn (string $className): ElasticsearchService => $app->make($className),
                config('elasticsearch.search_services')
            ));
        });
    }

    // TODO kpstya надо ли создать директивы для view

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
