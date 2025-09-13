<?php

declare(strict_types=1);

namespace App\Clients\Elasticsearch;

use App\Clients\Elasticsearch\Contracts\ElasticsearchClientContract;
use App\Exceptions\ElasticsearchApiException;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Utils;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class ElasticsearchClient implements ElasticsearchClientContract
{
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
        return Http::asJson()
            ->baseUrl($this->url)
            ->retry(3, 100)
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
        return Http::asJson()
            ->baseUrl($this->url)
            ->retry(3, 100)
            ->send('POST', $indexName.'/_bulk', [
                'body' => Utils::streamFor($body),
            ])
            ->onError(static function (PromiseInterface|Response $response) {
                throw ElasticsearchApiException::buildMessage($response->body(), $response->status());
            })
            ->json();

        // TODO kpstya добавить в конфиг надо ли отправлять письмо на почту
    }

    /**
     * @return array<string, bool>
     *
     * @throws ConnectionException
     * @throws ElasticsearchApiException
     */
    public function deleteIndex(string $indexName): array
    {
        return Http::asJson()
            ->baseUrl($this->url)
            ->retry(3, 100)
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
        return Http::asJson()
            ->baseUrl($this->url)
            ->retry(3, 100)
            ->post($indexName.'/_search', $body)
            ->onError(static function (PromiseInterface|Response $response) {
                throw ElasticsearchApiException::buildMessage($response->body(), $response->status());
            })
            ->json();
    }
}
