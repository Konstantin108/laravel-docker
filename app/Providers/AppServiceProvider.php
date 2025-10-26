<?php

namespace App\Providers;

use App\Clients\Elasticsearch\Contracts\ElasticsearchClientContract;
use App\Clients\Elasticsearch\ElasticsearchClient;
use App\Clients\Elasticsearch\ElasticsearchClientStub;
use App\Factories\Contracts\SourceDtoFactoryContract;
use App\Services\SourceDtoCollectionService;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ElasticsearchClientContract::class, static function (): ElasticsearchClientContract {
            return match (config('app.env')) {
                'testing' => new ElasticsearchClientStub,
                default => new ElasticsearchClient(config('elasticsearch.url'))
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
        //
    }
}
