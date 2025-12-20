<?php

namespace App\Providers;

use App\Clients\Elasticsearch\Contracts\ElasticsearchClientContract;
use App\Clients\Elasticsearch\ElasticsearchClient;
use App\Clients\Elasticsearch\ElasticsearchClientStub;
use App\Factories\Contracts\SourceDtoFactoryContract;
use App\Services\Elasticsearch\Dto\SettingsDto;
use App\Services\Elasticsearch\SourceDtoCollectionService;
use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->isLocal()) {
            $this->app->register(IdeHelperServiceProvider::class);
        }

        $this->app->bind(ElasticsearchClientContract::class, static function (): ElasticsearchClientContract {
            return match (config('app.env')) {
                'testing' => new ElasticsearchClientStub,
                default => new ElasticsearchClient(
                    config('elasticsearch.url'),
                    config('elasticsearch.user'),
                    config('elasticsearch.password'),
                    SettingsDto::from(config('elasticsearch.settings')),
                )
            };
        });

        $this->app->bind(SourceDtoCollectionService::class, static function (Application $app): SourceDtoCollectionService {
            return new SourceDtoCollectionService(...array_map(
                static fn (string $className): SourceDtoFactoryContract => $app->make($className),
                config('elasticsearch.source_dto_factories')
            ));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.query_log')) {
            DB::listen(static function (QueryExecuted $query): void {
                File::append(
                    storage_path('/logs/query.log'),
                    $query->sql.' ['.implode(', ', $query->bindings).']'.' time-'.$query->time.PHP_EOL
                );
            });
        }
    }
}
