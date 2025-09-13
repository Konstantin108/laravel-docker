<?php

declare(strict_types=1);

namespace App\Services\Elasticsearch;

use App\Dto\User\UserEnrichedDto;
use App\Entities\Elasticsearch\UserDocElement;
use App\Events\Search\UsersSearchIndexFilledEvent;
use App\Services\Elasticsearch\Abstract\ElasticsearchService;

class UsersIndexElasticsearchService extends ElasticsearchService
{
    protected const INDEX_NAME = 'users';

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
     * @return string[]
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

    public function fillSearchIndex(?int $count = null): mixed
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
                static::INDEX_NAME
            ))
            ->implode('');

        return tap(
            $this->client->bulkIndex($body, static::INDEX_NAME),
            static fn () => UsersSearchIndexFilledEvent::dispatch($users, static::INDEX_NAME)
        );
    }
}
