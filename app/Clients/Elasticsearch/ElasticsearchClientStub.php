<?php

declare(strict_types=1);

namespace App\Clients\Elasticsearch;

use App\Clients\Elasticsearch\Contracts\ElasticsearchClientContract;

class ElasticsearchClientStub implements ElasticsearchClientContract
{
    /**
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    public function createSearchIndex(array $body, string $indexName): array
    {
        return [
            'acknowledged' => true,
            'shards_acknowledged' => true,
            'index' => $indexName,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function bulkIndex(string $body, string $indexName): array
    {
        $lines = array_filter(explode("\n", $body));

        $items = [];
        $seqNumber = 0;
        for ($i = 0; $i < count($lines); $i += 2) {
            $operation = json_decode($lines[$i], true);
            $items[] = [
                'index' => [
                    '_index' => $operation['index']['_index'],
                    '_type' => '_doc',
                    '_id' => (string) $operation['index']['_id'],
                    '_version' => 1,
                    'result' => 'created',
                    '_shards' => [
                        'total' => 2,
                        'successful' => 1,
                        'failed' => 0,
                    ],
                    '_seq_no' => $seqNumber,
                    '_primary_term' => 1,
                    'status' => 201,
                ],
            ];
            $seqNumber++;
        }

        return [
            'took' => count($lines) * rand(1, 2),
            'errors' => false,
            'items' => $items,
        ];
    }

    // TODO kpstya реализовать мок этого метода и тест к нему

    /**
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    public function search(array $body, string $indexName): array
    {
        return [];
    }
}
