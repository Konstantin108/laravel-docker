<?php

declare(strict_types=1);

namespace App\Services;

use App\Clients\Elasticsearch\Contracts\ElasticsearchClientContract;
use App\Dto\Elasticsearch\PaginationRequestDto;
use App\Dto\User\UserEnrichedDto;
use App\Entities\Elasticsearch\UserDocElement;
use stdClass;

class ElasticsearchService
{
    private const USERS_INDEX = 'users';

    public function __construct(
        private readonly ElasticsearchClientContract $client,
        private readonly UserService $userService
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function createUsersSearchIndex(): array
    {
        $body = [
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

        return $this->client->createSearchIndex($body, self::USERS_INDEX);
    }

    public function fillUsersSearchIndex(?int $count = null): mixed
    {
        $users = $this->userService->getUsers($count);
        if ($users->isEmpty()) {
            return null;
        }

        $body = $users
            ->map(fn (UserEnrichedDto $user): string => $this->makeDocElement(
                (new UserDocElement(
                    id: $user->id,
                    name: $user->name,
                    email: $user->email,
                    emailVerifiedAt: $user->emailVerifiedAt,
                    reserveEmail: $user->reserveEmail,
                    phone: $user->phone,
                    telegram: $user->telegram,
                    createdAt: $user->createdAt,
                    updatedAt: $user->updatedAt,
                ))->toArray(),
                self::USERS_INDEX
            ))
            ->implode('');

        return $this->client->bulkIndex($body, self::USERS_INDEX);
    }

    /**
     * @return array<string, mixed>
     */
    public function findUsersInSearchIndex(PaginationRequestDto $requestDto): array
    {
        $body = $requestDto->search !== null
            ? $this->searchMultiMatch($requestDto)
            : $this->searchMatchAll($requestDto);

        return $this->client->search($body, self::USERS_INDEX);
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

    /**
     * @return array<string, mixed>
     */
    private function searchMultiMatch(PaginationRequestDto $requestDto): array
    {
        return [
            'size' => $requestDto->size,
            'from' => $requestDto->from,
            'query' => [
                'multi_match' => [
                    'query' => $requestDto->search,
                    'fields' => [
                        'name^5',
                        'email^4',
                        'reserve_email^3',
                        'telegram^2',
                        'phone^1',
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function searchMatchAll(PaginationRequestDto $requestDto): array
    {
        return [
            'size' => $requestDto->size,
            'from' => $requestDto->from,
            'query' => [
                'match_all' => new stdClass,
            ],
        ];
    }
}
