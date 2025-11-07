<?php

declare(strict_types=1);

namespace App\Services\Elasticsearch;

use App\Clients\Elasticsearch\Contracts\ElasticsearchClientContract;
use App\Entities\User\UserEnriched;
use App\EntityFactories\Elasticsearch\UserDocElementFactory;
use App\Events\Search\UsersSearchIndexFilledEvent;
use App\Services\Elasticsearch\Abstract\ElasticsearchService;
use App\Services\User\UserService;
use Illuminate\Support\HigherOrderTapProxy;

class UsersIndexElasticsearchService extends ElasticsearchService
{
    protected const INDEX_NAME = 'users';

    public function __construct(
        protected ElasticsearchClientContract $client,
        protected UserService $userService,
        private readonly UserDocElementFactory $userDocElementFactory
    ) {
        parent::__construct($client, $userService);
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
                    'email' => [
                        'type' => 'text',
                        'analyzer' => 'my_ngram_analyzer',
                    ],
                    'reserve_email' => [
                        'type' => 'text',
                        'analyzer' => 'my_ngram_analyzer',
                    ],
                    'phone' => [
                        'type' => 'text',
                        'analyzer' => 'my_ngram_analyzer',
                    ],
                    'telegram' => [
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
            'name^5',
            'email^4',
            'reserve_email^3',
            'telegram^2',
            'phone^1',
        ];
    }

    /**
     * @return HigherOrderTapProxy|array<string, mixed>|null
     */
    public function fillSearchIndex(?int $count = null): HigherOrderTapProxy|array|null
    {
        $users = $this->userService->getUsers($count);
        if ($users->isEmpty()) {
            return null;
        }

        $body = $users->map(fn (UserEnriched $user): string => $this->makeDocElement(
            $this->userDocElementFactory->make($user)->toArray(),
            static::INDEX_NAME
        ))
            ->implode('');

        return tap(
            $this->client->bulkIndex($body, static::INDEX_NAME),
            static fn (): ?array => UsersSearchIndexFilledEvent::dispatch($users, static::INDEX_NAME)
        );
    }
}
