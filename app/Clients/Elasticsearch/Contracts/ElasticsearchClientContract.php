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

    public function bulkIndex(string $body, string $indexName): mixed;

    /**
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    public function search(array $body, string $indexName): array;
}
