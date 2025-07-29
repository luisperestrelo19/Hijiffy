<?php

declare(strict_types=1);

namespace App\Services;

use Closure;
use Illuminate\Support\Facades\Cache;

class CacheService
{
    public int $defaultTtl;
    public bool $enabled;

    public function __construct(public string $cacheKeyPrefix)
    {
        $this->defaultTtl = (int) config('hijiffy.cache.ttl');
        $this->enabled    = (bool) config('hijiffy.cache.enabled');
    }

    /**
     * Caches the result of a callback using a unique key generated from the module name and sorted filters.
     * Stores a list of cache keys per module to allow grouped invalidation.
     * Its purpose is to cache search results for properties based on various filters.
     * It was a try of mimicking the tag functionality of Laravel's cache system.
     *
     * @param string  $module   The module name used as part of the cache key.
     * @param array   $filters  The filters to generate a unique cache key (sorted for consistency).
     * @param int     $ttl      Time to live for the cache entry, in seconds.
     * @param Closure $callback The callback whose result should be cached.
     * @return mixed            The cached value or the result of the callback.
     */
    public function cacheWithTag(string $module, array $data, Closure $callback)
    {
        if (!$this->enabled) {
            return $callback();
        }
        //just to ensure the filters are sorted for consistent cache keys to create always the same tag
        ksort($data);
        $tag = md5(json_encode($data));

        $tagKeysKey = "{$this->cacheKeyPrefix}_keys_{$module}";
        $taggedKeys = Cache::get($tagKeysKey, []);

        if (!in_array($tag, $taggedKeys)) {
            $taggedKeys[] = $tag;
            Cache::put($tagKeysKey, $taggedKeys, $this->defaultTtl);
        }

        return Cache::remember($tag, $this->defaultTtl, $callback);
    }

    public function forgetTag(string $tag)
    {
        if (!$this->enabled) {
            return;
        }
        $tagKeysKey = "{$this->cacheKeyPrefix}_keys_{$tag}";
        $taggedKeys = Cache::get($tagKeysKey, []);

        foreach ($taggedKeys as $key) {
            Cache::forget($key);
        }

        Cache::forget($tagKeysKey);
    }
}
