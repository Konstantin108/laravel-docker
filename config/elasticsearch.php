<?php

return [
    'url' => env('ELASTICSEARCH_URL'),

    'source_dto_factories' => [
        'users' => \App\Factories\SourceDtoFactory::class,
    ],
];
