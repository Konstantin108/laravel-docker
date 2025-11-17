<?php

declare(strict_types=1);

namespace App\Clients\Elasticsearch;

use App\Clients\Elasticsearch\Contracts\ElasticsearchClientContract;
use App\Clients\Elasticsearch\Exceptions\ElasticsearchApiException;
use App\Services\Elasticsearch\Dto\SettingsDto;
use GuzzleHttp\Psr7\Utils;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Throwable;

class ElasticsearchClient implements ElasticsearchClientContract
{
    private readonly string $url;

    public function __construct(
        string $url,
        private readonly string $user,
        private readonly string $password,
        private readonly SettingsDto $settings
    ) {
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
        return $this->execute(fn (): Response => $this->baseHttpRequest()
            ->put($indexName, $body)
        );
    }

    /**
     * @return array<string, mixed>
     *
     * @throws ElasticsearchApiException
     */
    public function bulkIndex(string $body, string $indexName): array
    {
        return $this->execute(fn (): Response => $this->baseHttpRequest()
            ->send('POST', $indexName.'/_bulk', [
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
        return $this->execute(fn (): Response => $this->baseHttpRequest()
            ->delete($indexName)
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
        return $this->execute(fn (): Response => $this->baseHttpRequest()
            ->post($indexName.'/_search', $body)
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
        return $this->execute(fn (): Response => $this->baseHttpRequest()
            ->post($indexName.'/_delete_by_query', $body)
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
        } catch (Throwable $e) {
            throw ElasticsearchApiException::buildMessage($e);
        }
    }

    private function baseHttpRequest(): PendingRequest
    {
        return Http::asJson()
            ->baseUrl($this->url)
            ->withBasicAuth($this->user, $this->password)
            ->timeout($this->settings->timeout)
            ->connectTimeout($this->settings->connectTimeout)
            ->retry(
                $this->settings->retryTimes,
                $this->settings->retrySleepMilliseconds
            );
    }
}
