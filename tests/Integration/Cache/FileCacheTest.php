<?php
/**
 * VCL for PHP 3.0
 *
 * Integration tests for FileCache
 */

declare(strict_types=1);

namespace VCL\Tests\Integration\Cache;

use PHPUnit\Framework\TestCase;
use VCL\Cache\FileCache;

class FileCacheTest extends TestCase
{
    private FileCache $cache;
    private string $cacheDir;

    protected function setUp(): void
    {
        $this->cacheDir = sys_get_temp_dir() . '/vcl_test_cache_' . uniqid();
        mkdir($this->cacheDir, 0755, true);

        $this->cache = new FileCache();
        $this->cache->CacheDir = $this->cacheDir;
    }

    protected function tearDown(): void
    {
        // Clean up cache directory
        $this->removeDirectory($this->cacheDir);
    }

    public function testSetAndGetValue(): void
    {
        $this->cache->set('test_key', 'test_value');

        $this->assertTrue($this->cache->has('test_key'));
        $this->assertSame('test_value', $this->cache->get('test_key'));
    }

    public function testGetNonExistentKeyReturnsNull(): void
    {
        $this->assertNull($this->cache->get('nonexistent'));
    }

    public function testDeleteRemovesValue(): void
    {
        $this->cache->set('to_delete', 'value');
        $this->assertTrue($this->cache->has('to_delete'));

        $this->cache->delete('to_delete');
        $this->assertFalse($this->cache->has('to_delete'));
    }

    public function testClearRemovesAllValues(): void
    {
        $this->cache->set('key1', 'value1');
        $this->cache->set('key2', 'value2');
        $this->cache->set('key3', 'value3');

        $this->cache->clear();

        $this->assertFalse($this->cache->has('key1'));
        $this->assertFalse($this->cache->has('key2'));
        $this->assertFalse($this->cache->has('key3'));
    }

    public function testExpiredCacheReturnsNull(): void
    {
        // Set with 1 second TTL
        $this->cache->set('expiring', 'value', 1);

        // Should exist immediately
        $this->assertTrue($this->cache->has('expiring'));

        // Wait for expiration
        sleep(2);

        // Should be expired now
        $this->assertNull($this->cache->get('expiring'));
    }

    /**
     * Recursively remove a directory.
     */
    private function removeDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            is_dir($path) ? $this->removeDirectory($path) : unlink($path);
        }
        rmdir($dir);
    }
}
