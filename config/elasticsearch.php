<?php

// TODO kpstya что если параметра ELASTICSEARCH_URL не будет в .env

return [
    'url' => env('ELASTICSEARCH_URL'),

    'settings' => [
        'timeout' => env('ELASTICSEARCH_TIMEOUT', 9),
        'connect_timeout' => env('ELASTICSEARCH_CONNECT_TIMEOUT', 3),
        'retry_times' => env('ELASTICSEARCH_RETRY_TIMES', 3),
        'retry_sleep_milliseconds' => env('ELASTICSEARCH_RETRY_SLEEP_MILLISECONDS', 100),
    ],

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
