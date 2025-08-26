<?php

return [
    'url' => env('ELASTICSEARCH_URL'),

    'hit_dto_factories' => [
        'users' => \App\Factories\UserHitDtoFactory::class,
    ],
];
