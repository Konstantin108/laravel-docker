<?php

declare(strict_types=1);

namespace App\Clients\Elasticsearch;

use App\Clients\Elasticsearch\Contracts\ElasticsearchClientContract;
use App\Clients\Elasticsearch\Exceptions\ElasticsearchApiException;
use Exception;

class ElasticsearchClientErrorStub implements ElasticsearchClientContract
{
    /**
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     *
     * @throws ElasticsearchApiException
     */
    public function createIndex(array $body, string $indexName): array
    {
        throw ElasticsearchApiException::buildMessage(
            new Exception('An error occurred while creating the index.')
        );
    }

    /**
     * @return array<string, mixed>
     *
     * @throws ElasticsearchApiException
     */
    public function bulkIndex(string $body, string $indexName): array
    {
        throw ElasticsearchApiException::buildMessage(
            new Exception('Index filling error.')
        );
    }

    /**
     * @return array<string, bool>
     *
     * @throws ElasticsearchApiException
     */
    public function deleteIndex(string $indexName): array
    {
        throw ElasticsearchApiException::buildMessage(
            new Exception('Index deleting error.')
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
        throw ElasticsearchApiException::buildMessage(
            new Exception('Index search error.')
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
        throw ElasticsearchApiException::buildMessage(
            new Exception('Index clearing error.')
        );
    }
}
