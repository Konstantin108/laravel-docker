<?php

declare(strict_types=1);

namespace App\Clients\Elasticsearch;

use App\Clients\Elasticsearch\Contracts\ElasticsearchClientContract;
use App\Exceptions\SearchIndexDoesNotExist;
use Faker\Factory;

class ElasticsearchClientStub implements ElasticsearchClientContract
{
    /**
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    public function createIndex(array $body, string $indexName): array
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
            'items' => $items ?? [],
        ];
    }

    /**
     * @return array<string, bool>
     */
    public function deleteIndex(string $indexName): array
    {
        return ['acknowledged' => true];
    }

    /**
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     *
     * @throws SearchIndexDoesNotExist
     */
    public function search(array $body, string $indexName): array
    {
        $modelName = config('elasticsearch.search_index_models.'.$indexName);
        $service = config('elasticsearch.model_services.'.$indexName);

        if ($modelName === null || $service === null) {
            throw SearchIndexDoesNotExist::buildMessage($indexName);
        }

        $elements = $modelName::query()
            ->where('id', '>', $body['from'])
            ->limit($body['size'])
            ->get()
            ->map(static fn ($element) => app($service)->enrich($element));

        $maxScore = Factory::create()
            ->randomFloat(6, 20, 70);

        return [
            'took' => rand(1, 30),
            'timed_out' => false,
            '_shards' => [
                'total' => 1,
                'successful' => 1,
                'skipped' => 0,
                'failed' => 0,
            ],
            'hits' => [
                'total' => [
                    'value' => count($elements),
                    'relation' => 'eq',
                ],
                'max_score' => $maxScore,
                'hits' => $elements->map(static fn ($element, $key): array => [
                    '_index' => $indexName,
                    '_type' => '_doc',
                    '_id' => (string) $element->id,
                    '_score' => $maxScore - $key * rand(1, 9),
                    '_source' => $element->toArray(),
                ])->toArray(),
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    public function clearIndex(array $body, string $indexName): array
    {
        // TODO kpstya реализовать
        return [];
    }
}
