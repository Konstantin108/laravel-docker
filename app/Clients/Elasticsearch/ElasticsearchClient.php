<?php

declare(strict_types=1);

namespace App\Clients\Elasticsearch;

use App\Clients\Elasticsearch\Contracts\ElasticsearchClientContract;
use App\Ship\Exceptions\ElasticsearchApiException;
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
    public function createSearchIndex(array $body, string $indexName): array
    {
        return Http::asJson()
            ->withUrlParameters([
                'url' => $this->url,
            ])
            ->retry(3, 100)
            ->put('{+url}/'.$indexName, $body)
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
            ->withUrlParameters([
                'url' => $this->url,
            ])
            ->retry(3, 100)
            ->send('POST', '{+url}/'.$indexName.'/_bulk', [
                'body' => Utils::streamFor($body),
            ])
            ->onError(static function (PromiseInterface|Response $response) {
                throw ElasticsearchApiException::buildMessage($response->body(), $response->status());
            })
            ->json();
    }
}
