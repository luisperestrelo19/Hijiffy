<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\CacheService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class CacheServiceTest extends TestCase
{
    private CacheService $cacheService;
    private string $cacheKeyPrefix = 'test_prefix';

    public function test_constructor_sets_default_ttl_from_config(): void
    {
        Config::set('hijiffy.cache.ttl', 1200);
        $service = new CacheService('test');

        $this->assertEquals(1200, $service->defaultTtl);
    }

    public function test_constructor_sets_cache_key_prefix(): void
    {
        $prefix  = 'custom_prefix';
        $service = new CacheService($prefix);

        $this->assertEquals($prefix, $service->cacheKeyPrefix);
    }

    public function test_cache_with_tag_stores_and_retrieves_data(): void
    {
        $module         = 'properties';
        $data           = ['city' => 'Lisbon', 'guests' => 2];
        $expectedResult = 'test_result';

        $callback = fn () => $expectedResult;

        $result = $this->cacheService->cacheWithTag($module, $data, $callback);

        $this->assertEquals($expectedResult, $result);
    }

    public function test_cache_with_tag_creates_consistent_keys_for_same_data(): void
    {
        $module = 'properties';
        $data1  = ['city' => 'Lisbon', 'guests' => 2];
        $data2  = ['guests' => 2, 'city' => 'Lisbon']; // Same data, different order

        $result1 = $this->cacheService->cacheWithTag($module, $data1, fn () => 'result1');
        $result2 = $this->cacheService->cacheWithTag($module, $data2, fn () => 'result2');

        $this->assertEquals('result1', $result1);
        $this->assertEquals('result1', $result2);
    }

    public function test_cache_with_tag_creates_different_keys_for_different_data(): void
    {
        $module = 'properties';
        $data1  = ['city' => 'Lisbon', 'guests' => 2];
        $data2  = ['city' => 'Porto', 'guests' => 2];

        $result1 = $this->cacheService->cacheWithTag($module, $data1, fn () => 'result1');
        $result2 = $this->cacheService->cacheWithTag($module, $data2, fn () => 'result2');

        $this->assertEquals('result1', $result1);
        $this->assertEquals('result2', $result2);
    }

    public function test_forget_tag_removes_all_cached_data_for_module(): void
    {
        $module = 'properties';
        $data1  = ['city' => 'Lisbon', 'guests' => 2];
        $data2  = ['city' => 'Porto', 'guests' => 3];

        // Cache some data
        $result1 = $this->cacheService->cacheWithTag($module, $data1, fn () => 'result1');
        $result2 = $this->cacheService->cacheWithTag($module, $data2, fn () => 'result2');

        $this->assertEquals('result1', $result1);
        $this->assertEquals('result2', $result2);

        // Verify data is cached
        $cachedResult1 = $this->cacheService->cacheWithTag($module, $data1, fn () => 'new_result1');
        $cachedResult2 = $this->cacheService->cacheWithTag($module, $data2, fn () => 'new_result2');

        $this->assertEquals('result1', $cachedResult1); // Should return cached
        $this->assertEquals('result2', $cachedResult2); // Should return cached

        // Clear the cache for this module
        $this->cacheService->forgetTag($module);

        // Verify cache is cleared
        $newResult1 = $this->cacheService->cacheWithTag($module, $data1, fn () => 'fresh_result1');
        $newResult2 = $this->cacheService->cacheWithTag($module, $data2, fn () => 'fresh_result2');

        $this->assertEquals('fresh_result1', $newResult1); // Should execute callback
        $this->assertEquals('fresh_result2', $newResult2); // Should execute callback
    }

    public function test_forget_tag_removes_tag_keys_list(): void
    {
        $module = 'properties';
        $data   = ['city' => 'Lisbon', 'guests' => 2];

        $this->cacheService->cacheWithTag($module, $data, fn () => 'result');

        $tagKeysKey = "{$this->cacheKeyPrefix}_keys_{$module}";

        $taggedKeys = Cache::get($tagKeysKey, []);
        $this->assertNotEmpty($taggedKeys);

        $this->cacheService->forgetTag($module);

        $taggedKeysAfter = Cache::get($tagKeysKey, []);
        $this->assertEmpty($taggedKeysAfter);
    }

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('hijiffy.cache.ttl', 600);

        $this->cacheService = new CacheService($this->cacheKeyPrefix);
    }

    protected function tearDown(): void
    {
        Cache::flush();
        parent::tearDown();
    }
}
