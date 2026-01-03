<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\Cache;

use PHPUnit\Framework\TestCase;
use VCL\Cache\FileCache;

class FileCacheTest extends TestCase
{
    private FileCache $cache;
    private string $testCacheDir;

    protected function setUp(): void
    {
        $this->testCacheDir = sys_get_temp_dir() . '/vcl_test_cache_' . uniqid();
        $this->cache = new FileCache();
        $this->cache->Name = 'TestFileCache';
        $this->cache->CacheDir = $this->testCacheDir;
    }

    protected function tearDown(): void
    {
        // Clean up test cache directory
        $this->cache->clear();
        if (is_dir($this->testCacheDir)) {
            @rmdir($this->testCacheDir);
        }
    }

    public function testDefaultTTL(): void
    {
        $this->assertSame(3600, $this->cache->DefaultTTL);
    }

    public function testSetDefaultTTL(): void
    {
        $this->cache->DefaultTTL = 7200;
        $this->assertSame(7200, $this->cache->DefaultTTL);
    }

    public function testCacheDirProperty(): void
    {
        $dir = sys_get_temp_dir() . '/vcl_custom_cache_' . uniqid();
        $this->cache->CacheDir = $dir;
        $this->assertSame($dir, $this->cache->CacheDir);

        // Clean up
        @rmdir($dir);
    }

    public function testCacheDirCreatesDirectory(): void
    {
        $dir = sys_get_temp_dir() . '/vcl_new_cache_dir_' . uniqid();
        $this->assertFalse(is_dir($dir));

        $this->cache->CacheDir = $dir;
        $this->assertTrue(is_dir($dir));

        // Clean up
        @rmdir($dir);
    }

    public function testSetAndGet(): void
    {
        $this->cache->set('test_key', 'test_value', 3600);
        $this->assertSame('test_value', $this->cache->get('test_key'));
    }

    public function testGetNonExistentKey(): void
    {
        $this->assertNull($this->cache->get('nonexistent_key'));
    }

    public function testHas(): void
    {
        $this->assertFalse($this->cache->has('test_key'));
        $this->cache->set('test_key', 'test_value', 3600);
        $this->assertTrue($this->cache->has('test_key'));
    }

    public function testDelete(): void
    {
        $this->cache->set('test_key', 'test_value', 3600);
        $this->assertTrue($this->cache->has('test_key'));

        $this->cache->delete('test_key');
        $this->assertFalse($this->cache->has('test_key'));
    }

    public function testClear(): void
    {
        $this->cache->set('key1', 'value1', 3600);
        $this->cache->set('key2', 'value2', 3600);

        $this->assertTrue($this->cache->has('key1'));
        $this->assertTrue($this->cache->has('key2'));

        $this->cache->clear();

        $this->assertFalse($this->cache->has('key1'));
        $this->assertFalse($this->cache->has('key2'));
    }

    public function testExpiredCacheReturnsNull(): void
    {
        // Set with very short TTL
        $this->cache->set('expired_key', 'expired_value', 1);

        // Wait for expiration (use longer sleep to avoid flaky tests)
        sleep(3);

        $this->assertNull($this->cache->get('expired_key'));
    }

    public function testZeroTTLDoesNotExpire(): void
    {
        $this->cache->set('permanent_key', 'permanent_value', 0);
        $this->assertSame('permanent_value', $this->cache->get('permanent_key'));
    }

    public function testGarbageCollection(): void
    {
        // Set with very short TTL
        $this->cache->set('gc_key', 'gc_value', 1);

        // Wait for expiration (use longer sleep to avoid flaky tests)
        sleep(3);

        $removed = $this->cache->gc();
        $this->assertGreaterThanOrEqual(1, $removed);
    }

    public function testIsComponent(): void
    {
        $this->assertInstanceOf(\VCL\Core\Component::class, $this->cache);
    }
}
