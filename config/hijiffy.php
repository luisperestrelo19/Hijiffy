<?php

declare(strict_types=1);

return [
    'rate_limits' => [
        'api' => [
            'limit' => env('HIIJIFFY_RATE_LIMIT_API', 60),
        ],
        'sync-endpoint' => [
            'limit' => env('HIIJIFFY_RATE_LIMIT_SYNC', 60),
        ],
        'guest' => [
            'limit' => env('HIIJIFFY_RATE_LIMIT_GUEST', 60),
        ],
    ],
];
