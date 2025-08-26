<?php

return [
    'url' => env('ELASTICSEARCH_URL'),

    // TODO index работает неправильно

    'source_dto_factories' => [
        'users' => \App\Factories\UserSourceDtoFactory::class,
    ],
];
