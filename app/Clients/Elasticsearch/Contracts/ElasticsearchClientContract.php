<?php

declare(strict_types=1);

namespace App\Clients\Elasticsearch\Contracts;

interface ElasticsearchClientContract
{
    // TODO kpstya добавить метод для удаления всех документов в индексе
    // используются теже фильтры, что и для поиска
    /*
    запрос: users/_delete_by_query  POST
    тело запроса:
    {
        "query": {
            "match_all": {}
        }
    }'

    для удаления не нужно передавать параметры size и from, они будут проигнорированы
    нужно просто удалять все документы, это будет команда
    написать тесты
    */

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
