<?php

declare(strict_types=1);

namespace App\Clients\Elasticsearch;

use App\Clients\Elasticsearch\Contracts\ElasticsearchClientContract;
use App\Exceptions\ElasticsearchApiException;
use Exception;
use GuzzleHttp\Psr7\Utils;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use stdClass;

class ElasticsearchClient implements ElasticsearchClientContract
{
    // TODO kpstya применить рекомендации от Каната
    // TODO kpstya APP_MAINTENANCE_DRIVER - что это за параметр (.env)
    // TODO kpstya изучить параметры в .env

    // TODO kpstya возможно добавить креды для Elasticsearch

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
     */
    public function createIndex(array $body, string $indexName): array
    {
        return $this->execute(fn (): Response => $this
            ->baseHttpRequest()->put($indexName, $body)
        );
    }

    /**
     * @return array<string, mixed>
     *
     * @throws ElasticsearchApiException
     */
    public function bulkIndex(string $body, string $indexName): array
    {
        return $this->execute(fn (): Response => $this
            ->baseHttpRequest()->send('POST', $indexName.'/_bulk', [
                'body' => Utils::streamFor($body),
            ])
        );
    }

    /**
     * @return array<string, bool>
     *
     * @throws ElasticsearchApiException
     */
    public function deleteIndex(string $indexName): array
    {
        return $this->execute(fn (): Response => $this
            ->baseHttpRequest()->delete($indexName)
        );
    }

    /**
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     *
     * @throws ElasticsearchApiException
     */
    public function search(array $body, string $indexName): array
    {
        return $this->execute(fn (): Response => $this
            ->baseHttpRequest()->post($indexName.'/_search', $body)
        );
    }

    /**
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     *
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

        return $this->execute(fn (): Response => $this
            ->baseHttpRequest()->post($indexName.'/_delete_by_query', $body)
        );
    }

    /**
     * @return array<string, mixed>
     *
     * @throws ElasticsearchApiException
     */
    private function execute(callable $request): array
    {
        try {
            return $request()->json();
        } catch (Exception $e) {
            throw ElasticsearchApiException::buildMessage($e);
        }
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
