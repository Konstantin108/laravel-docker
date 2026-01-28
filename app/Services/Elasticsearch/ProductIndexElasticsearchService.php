<?php

declare(strict_types=1);

namespace App\Services\Elasticsearch;

use App\Clients\Elasticsearch\Contracts\ElasticsearchClientContract;
use App\Events\Elasticsearch\SearchIndexFilledEvent;
use App\Services\Elasticsearch\Abstract\ElasticsearchService;
use App\Services\Elasticsearch\Entities\BulkIndexResult;
use App\Services\Elasticsearch\Factories\BulkIndexResultFactory;
use App\Services\Elasticsearch\Factories\SearchResultFactory;
use App\Services\Product\Dto\IndexDto;
use App\Services\Product\Entities\ProductEnriched;
use App\Services\Product\ProductService;
use Illuminate\Contracts\Events\Dispatcher;

class ProductIndexElasticsearchService extends ElasticsearchService
{
    protected const INDEX_NAME = 'products';

    public function __construct(
        protected ElasticsearchClientContract $client,
        protected SearchResultFactory $searchResultFactory,
        private readonly Dispatcher $dispatcher,
        private readonly ProductService $productService,
        private readonly BulkIndexResultFactory $bulkIndexResultFactory,
    ) {
        parent::__construct($client, $searchResultFactory);
    }

    protected function indexName(): string
    {
        return static::INDEX_NAME;
    }

    /**
     * @return array<string, mixed>
     */
    protected function bodyIndexCreate(): array
    {
        return [
            'settings' => [
                'analysis' => [
                    'filter' => [
                        'my_ngram_filter' => [
                            'type' => 'ngram',
                            'min_gram' => 3,
                            'max_gram' => 4,
                        ],
                    ],
                    'analyzer' => [
                        'my_ngram_analyzer' => [
                            'type' => 'custom',
                            'tokenizer' => 'standard',
                            'filter' => [
                                'lowercase',
                                'my_ngram_filter',
                            ],
                        ],
                    ],
                ],
            ],
            'mappings' => [
                'properties' => [
                    'name' => [
                        'type' => 'text',
                        'analyzer' => 'my_ngram_analyzer',
                    ],
                    'description' => [
                        'type' => 'text',
                        'analyzer' => 'my_ngram_analyzer',
                    ],
                ],
            ],
        ];
    }

    /**
     * @return list<string>
     */
    protected function multiMatchFieldsSettings(): array
    {
        return [
            'name^2',
            'description^1',
        ];
    }

    public function fillSearchIndex(?int $limit = null): ?BulkIndexResult
    {
        $products = $this->productService->getProducts(new IndexDto(limit: $limit));
        if ($products->isEmpty()) {
            return null;
        }

        $body = $products->map(fn (ProductEnriched $product): string => $this->makeDocElement(
            $product->toArray(),
            static::INDEX_NAME
        ))
            ->implode('');

        $result = $this->client->bulkIndex($body, static::INDEX_NAME);

        return tap(
            $this->bulkIndexResultFactory->createFromArray($result),
            fn (): ?array => $this->dispatcher->dispatch(new SearchIndexFilledEvent($products, static::INDEX_NAME))
        );
    }
}
