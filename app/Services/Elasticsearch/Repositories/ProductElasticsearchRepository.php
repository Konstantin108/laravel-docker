<?php

declare(strict_types=1);

namespace App\Services\Elasticsearch\Repositories;

use App\Clients\Elasticsearch\Contracts\ElasticsearchClientContract;
use App\Enums\SortedByEnum;
use App\Events\Elasticsearch\SearchIndexFilledEvent;
use App\Services\Elasticsearch\Entities\BulkIndexResult;
use App\Services\Elasticsearch\Factories\BulkIndexResultFactory;
use App\Services\Elasticsearch\Factories\SearchResultFactory;
use App\Services\Elasticsearch\Repositories\Abstract\BaseElasticsearchRepository;
use App\Services\Product\Dto\FilterDto;
use App\Services\Product\Entities\ProductEnriched;
use App\Services\Product\ProductService;
use Illuminate\Contracts\Events\Dispatcher;

class ProductElasticsearchRepository extends BaseElasticsearchRepository
{
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
        return 'products';
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
        $products = $this->productService->getList(new FilterDto(
            sortedBy: SortedByEnum::ASC,
            limit: $limit
        ));
        if ($products->isEmpty()) {
            return null;
        }

        $body = $products->map(fn (ProductEnriched $product): string => $this->makeDocElement(
            $product->toArray(),
            $this->indexName()
        ))
            ->implode('');

        $result = $this->client->bulkIndex($body, $this->indexName());

        return tap(
            $this->bulkIndexResultFactory->make($result),
            fn (): ?array => $this->dispatcher->dispatch(new SearchIndexFilledEvent($products, $this->indexName()))
        );
    }
}
