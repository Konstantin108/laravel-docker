<?php

namespace App\Providers;

use App\Clients\Elasticsearch\Contracts\ElasticsearchClientContract;
use App\Clients\Elasticsearch\ElasticsearchClient;
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
        $this->app->bind(ElasticsearchClientContract::class, static function () {
            return new ElasticsearchClient(config('elasticsearch.url'));
        });

        // TODO kpstya предусмотреть если на entity нет фабрики или фабрик нет вовсе

        // TODO kpstya добавить вторую фабрику для dto

        $this->app->bind(SourceDtoCollectionService::class, static function (Application $app) {
            return new SourceDtoCollectionService(...array_map(
                static fn (string $className) => $app->make($className),
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
