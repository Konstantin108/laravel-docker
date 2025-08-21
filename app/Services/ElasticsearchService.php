<?php

declare(strict_types=1);

namespace App\Services;

use App\Clients\ElasticsearchClient;
use App\Dto\User\UserEnrichedDto;
use App\Entities\Elasticsearch\UserDocElement;
use App\Ship\Exceptions\ElasticsearchApiException;
use Illuminate\Http\Client\ConnectionException;

class ElasticsearchService
{
    private const string USERS_INDEX = 'users';

    public function __construct(
        private readonly ElasticsearchClient $client,
        private readonly UserService $userService
    ) {}

    /**
     * @throws ElasticsearchApiException
     * @throws ConnectionException
     */
    public function createUsersSearchIndex(): void
    {
        $body = [
            'settings' => [
                'analysis' => [
                    'tokenizer' => [
                        'edge_ngram_tokenizer' => [
                            'type' => 'edge_ngram',
                            'min_gram' => 2,
                            'max_gram' => 20,
                            'token_chars' => ['letter', 'digit'],
                        ],
                    ],
                    'analyzer' => [
                        'edge_ngram_analyzer' => [
                            'type' => 'custom',
                            'tokenizer' => 'edge_ngram_tokenizer',
                            'filter' => ['lowercase'],
                        ],
                        'edge_ngram_search_analyzer' => [
                            'type' => 'custom',
                            'tokenizer' => 'standard',
                            'filter' => ['lowercase'],
                        ],
                    ],
                ],
            ],
            'mappings' => [
                'properties' => [
                    'id' => [
                        'type' => 'integer',
                    ],
                    'name' => [
                        'type' => 'text',
                        'analyzer' => 'edge_ngram_analyzer',
                        'search_analyzer' => 'edge_ngram_search_analyzer',
                    ],
                    'email' => [
                        'type' => 'text',
                        'analyzer' => 'edge_ngram_analyzer',
                        'search_analyzer' => 'edge_ngram_search_analyzer',
                    ],
                    'reserve_email' => [
                        'type' => 'text',
                        'analyzer' => 'edge_ngram_analyzer',
                        'search_analyzer' => 'edge_ngram_search_analyzer',
                    ],
                    'phone' => [
                        'type' => 'text',
                        'analyzer' => 'edge_ngram_analyzer',
                        'search_analyzer' => 'edge_ngram_search_analyzer',
                    ],
                    'telegram' => [
                        'type' => 'text',
                        'analyzer' => 'edge_ngram_analyzer',
                        'search_analyzer' => 'edge_ngram_search_analyzer',
                    ],
                ],
            ],
        ];

        $this->client->createSearchIndex($body, self::USERS_INDEX);
    }

    /**
     * @throws ConnectionException
     * @throws ElasticsearchApiException
     */
    public function fillUsersSearchIndex(?int $count = null): void
    {
        $body = $this->userService
            ->getUsers($count)
            ->map(fn (UserEnrichedDto $user): string => $this->makeDocElement(
                (new UserDocElement(
                    id: $user->id,
                    name: $user->name,
                    email: $user->email,
                    reserveEmail: $user->email,
                    phone: $user->phone,
                    telegram: $user->telegram
                ))->toArray(),
                self::USERS_INDEX
            ))
            ->implode('');

        $this->client->bulkIndex($body, self::USERS_INDEX);
    }

    /**
     * @param  array<string, int|string>  $data
     */
    private function makeDocElement(array $data, string $indexName): string
    {
        return sprintf(
            "%s\n%s\n",
            json_encode([
                'index' => [
                    '_index' => $indexName,
                    '_id' => $data['id'],
                ],
            ]),
            json_encode($data),
        );
    }
}
