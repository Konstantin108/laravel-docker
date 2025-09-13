<?php

return [
    'url' => env('ELASTICSEARCH_URL'),
    'send_report_to_email' => env('ELASTICSEARCH_SEND_REPORT_TO_EMAIL', false),

    'source_dto_factories' => [
        'users' => \App\Factories\UserSourceDtoFactory::class,
    ],

    'search_index_models' => [
        'users' => \App\Models\User::class,
    ],

    'model_services' => [
        'users' => \App\Services\UserService::class,
    ],
];
