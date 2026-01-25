<?php

declare(strict_types=1);

namespace App\Clients\Elasticsearch;

use App\Clients\Elasticsearch\Contracts\ElasticsearchClientContract;
use App\Models\Contracts\SearchableContract;
use App\Services\Contracts\SearchableSourceContract;
use App\Services\Elasticsearch\Enums\SearchIndexEnum;
use App\Services\Elasticsearch\Exceptions\SearchIndexException;
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
        $linesCount = count($lines);
        for ($i = 0; $i < $linesCount; $i += 2) {
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
            'took' => $linesCount * rand(1, 2),
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
     * @throws SearchIndexException
     */
    public function search(array $body, string $indexName): array
    {
        $model = SearchIndexEnum::from($indexName)->getModel();
        $service = SearchIndexEnum::from($indexName)->getModelService();

        $elements = $model::query()
            ->where('id', '>', $body['from'])
            ->limit($body['size'])
            ->get()
            ->map(static function (SearchableContract $element) use ($service): SearchableSourceContract {
                return app($service)->enrich($element);
            });

        $maxScore = Factory::create()->randomFloat(6, 20, 70);

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
                    'value' => $elements->count(),
                    'relation' => 'eq',
                ],
                'max_score' => $maxScore,
                'hits' => $elements->map(static fn (SearchableSourceContract $element, int $key): array => [
                    '_index' => $indexName,
                    '_type' => '_doc',
                    '_id' => (string) $element->getId(),
                    '_score' => $maxScore - $key * rand(1, 9),
                    '_source' => $element->toArray(),
                ])->toArray(),
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     *
     * @throws SearchIndexException
     */
    public function clearIndex(array $body, string $indexName): array
    {
        $model = SearchIndexEnum::from($indexName)->getModel();
        $elementsCount = $model::query()->count();

        return [
            'took' => $elementsCount + rand(1, 5),
            'timed_out' => false,
            'total' => $elementsCount,
            'deleted' => $elementsCount,
            'batches' => 1,
            'version_conflicts' => 0,
            'noops' => 0,
            'retries' => [
                'bulk' => 0,
                'search' => 0,
            ],
            'throttled_millis' => 0,
            'requests_per_second' => -1,
            'throttled_until_millis' => 0,
            'failures' => [],
        ];
    }
}
