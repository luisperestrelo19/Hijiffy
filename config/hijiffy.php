<?php

declare(strict_types=1);

return [
    'cache' => [
        'enabled'                    => env('HIIJIFFY_CACHE_ENABLED', true), // Enable or disable caching
        'module_prefix_availability' => 'properties_search',
        'ttl'                        => env('HIIJIFFY_CACHE_TTL', 600), // Default TTL for cache entries in seconds
    ],
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
