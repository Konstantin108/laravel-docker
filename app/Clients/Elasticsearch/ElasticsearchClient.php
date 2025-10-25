<?php

declare(strict_types=1);

namespace App\Clients\Elasticsearch;

use App\Clients\Elasticsearch\Contracts\ElasticsearchClientContract;
use App\Exceptions\ElasticsearchApiException;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Utils;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use stdClass;

class ElasticsearchClient implements ElasticsearchClientContract
{
    // TODO kpstya применить рекомендации от Каната
    // TODO kpstya APP_MAINTENANCE_DRIVER - что это за параметр (.env)
    // TODO kpstya изучить параметры в .env

    private readonly string $url;

    public function __construct(string $url)
    {
        $this->url = rtrim($url, '/');
    }

    /**
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     *
     * @throws ElasticsearchApiException
     * @throws ConnectionException
     */
    public function createIndex(array $body, string $indexName): array
    {
        return $this
            ->baseHttpRequest()
            ->put($indexName, $body)
            ->onError(static function (PromiseInterface|Response $response) {
                throw ElasticsearchApiException::buildMessage($response->body(), $response->status());
            })
            ->json();
    }

    /**
     * @throws ElasticsearchApiException
     * @throws ConnectionException
     */
    public function bulkIndex(string $body, string $indexName): mixed
    {
        return $this
            ->baseHttpRequest()
            ->send('POST', $indexName.'/_bulk', [
                'body' => Utils::streamFor($body),
            ])
            ->onError(static function (PromiseInterface|Response $response) {
                throw ElasticsearchApiException::buildMessage($response->body(), $response->status());
            })
            ->json();
    }

    /**
     * @return array<string, bool>
     *
     * @throws ConnectionException
     * @throws ElasticsearchApiException
     */
    public function deleteIndex(string $indexName): array
    {
        return $this
            ->baseHttpRequest()
            ->delete($indexName)
            ->onError(static function (PromiseInterface|Response $response) {
                throw ElasticsearchApiException::buildMessage($response->body(), $response->status());
            })
            ->json();
    }

    /**
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     *
     * @throws ConnectionException
     * @throws ElasticsearchApiException
     */
    public function search(array $body, string $indexName): array
    {
        return $this
            ->baseHttpRequest()
            ->post($indexName.'/_search', $body)
            ->onError(static function (PromiseInterface|Response $response) {
                throw ElasticsearchApiException::buildMessage($response->body(), $response->status());
            })
            ->json();
    }

    /**
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     *
     * @throws ConnectionException
     * @throws ElasticsearchApiException
     */
    public function clearIndex(array $body, string $indexName): array
    {
        // TODO kpstya вынести в сервис и написать тесты
        $body = [
            'query' => [
                'match_all' => new stdClass,
            ],
        ];

        return $this
            ->baseHttpRequest()
            ->post($indexName.'/_delete_by_query', $body)
            ->onError(static function (PromiseInterface|Response $response) {
                throw ElasticsearchApiException::buildMessage($response->body(), $response->status());
            })
            ->json();
    }

    private function baseHttpRequest(): PendingRequest
    {
        return Http::asJson()
            ->baseUrl($this->url)
            ->timeout(9)
            ->connectTimeout(3)
            ->retry(3, 100);
    }
}
