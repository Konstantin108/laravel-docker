<?php

namespace App\Providers;

use App\Clients\Elasticsearch\Contracts\ElasticsearchClientContract;
use App\Clients\Elasticsearch\ElasticsearchClient;
use App\Services\HitDtoCollectionService;
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

        // TODO kpstya надо добавить static для стрелок, добавить вторую фабрику для dto

        $this->app->bind(HitDtoCollectionService::class, static function (Application $app) {
            return new HitDtoCollectionService(...array_map(
                static fn (string $className) => $app->make($className),
                config('elasticsearch.hit_dto_factories')
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
