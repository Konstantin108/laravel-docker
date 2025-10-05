<?php

declare(strict_types=1);

namespace App\Clients\Elasticsearch\Contracts;

interface ElasticsearchClientContract
{
    // TODO kpstya возможно добавить метод для удаления всех документов в индексе
    // используются теже фильтры, что и для поиска
    /*
    запрос: users/_delete_by_query  POST
    тело запроса:
    {
        "query": {
            "match_all": {}
        }
    }'
    */

    /**
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    public function createIndex(array $body, string $indexName): array;

    public function bulkIndex(string $body, string $indexName): mixed;

    /**
     * @return array<string, bool>
     */
    public function deleteIndex(string $indexName): array;

    /**
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    public function search(array $body, string $indexName): array;
}
