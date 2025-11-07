<?php

declare(strict_types=1);

namespace App\Clients\Elasticsearch\Contracts;

interface ElasticsearchClientContract
{
    /**
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    public function createIndex(array $body, string $indexName): array;

    /**
     * @return array<string, mixed>
     */
    public function bulkIndex(string $body, string $indexName): array;

    /**
     * @return array<string, bool>
     */
    public function deleteIndex(string $indexName): array;

    /**
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    public function search(array $body, string $indexName): array;

    /**
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    public function clearIndex(array $body, string $indexName): array;
}
