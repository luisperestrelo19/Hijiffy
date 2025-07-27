<?php

declare(strict_types=1);

return [
    'rate_limits' => [
        'api' => [
            'limit' => env('HIIJIFFY_RATE_LIMIT_API', 100),
        ],
        'sync-endpoint' => [
            'limit' => env('HIIJIFFY_RATE_LIMIT_SYNC', 10),
        ],
        'guest' => [
            'limit' => env('HIIJIFFY_RATE_LIMIT_GUEST', 5),
        ],
    ],
];
