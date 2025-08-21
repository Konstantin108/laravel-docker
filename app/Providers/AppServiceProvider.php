<?php

namespace App\Providers;

use App\Clients\ElasticsearchClient;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ElasticsearchClient::class, static function () {
            return new ElasticsearchClient(config('elasticsearch.url'));
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
