<?php
/**
 * VCL for PHP
 *
 * Copyright (c) 2004-2008 qadram software S.L.
 * Copyright (c) 2026 Gunnar Beushausen
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 */

declare(strict_types=1);

namespace VCL\Cache;

/**
 * File-based cache implementation.
 *
 * Stores cached content in the filesystem.
 *
 * Example usage:
 * ```php
 * $cache = new FileCache($this);
 * $cache->CacheDir = '/tmp/vcl_cache';
 * $cache->DefaultTTL = 3600; // 1 hour
 *
 * // Cache a value
 * $cache->set('my_key', 'cached content', 600);
 *
 * // Retrieve a value
 * $content = $cache->get('my_key');
 * ```
 */
class FileCache extends Cache
{
    protected string $_cachedir = '';

    // =========================================================================
    // PROPERTY HOOKS
    // =========================================================================

    public string $CacheDir {
        get => $this->_cachedir;
        set {
            $this->_cachedir = rtrim($value, '/\\');
            if (!is_dir($this->_cachedir)) {
                @mkdir($this->_cachedir, 0755, true);
            }
        }
    }

    // =========================================================================
    // CONSTRUCTOR
    // =========================================================================

    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);

        // Default to system temp directory
        $this->_cachedir = sys_get_temp_dir() . '/vcl_cache';
        if (!is_dir($this->_cachedir)) {
            @mkdir($this->_cachedir, 0755, true);
        }
    }

    // =========================================================================
    // CacheInterface IMPLEMENTATION
    // =========================================================================

    /**
     * Get cached content for a key.
     */
    public function get(string $key): ?string
    {
        $file = $this->getCacheFile($key);

        if (!file_exists($file)) {
            return null;
        }

        $data = @file_get_contents($file);
        if ($data === false) {
            return null;
        }

        $cache = @unserialize($data);
        if ($cache === false) {
            return null;
        }

        // Check TTL
        if ($cache['expires'] > 0 && $cache['expires'] < time()) {
            $this->delete($key);
            return null;
        }

        return $cache['content'];
    }

    /**
     * Set cached content for a key.
     */
    public function set(string $key, string $content, int $ttl = 0): void
    {
        $file = $this->getCacheFile($key);

        $cache = [
            'created' => time(),
            'expires' => $ttl > 0 ? time() + $ttl : 0,
            'content' => $content,
        ];

        @file_put_contents($file, serialize($cache), LOCK_EX);
    }

    /**
     * Check if a key exists in cache.
     */
    public function has(string $key): bool
    {
        return $this->get($key) !== null;
    }

    /**
     * Delete a cached item.
     */
    public function delete(string $key): void
    {
        $file = $this->getCacheFile($key);
        if (file_exists($file)) {
            @unlink($file);
        }
    }

    /**
     * Clear all cached items.
     */
    public function clear(): void
    {
        $files = glob($this->_cachedir . '/*.cache');
        if ($files) {
            foreach ($files as $file) {
                @unlink($file);
            }
        }
    }

    // =========================================================================
    // PROTECTED METHODS
    // =========================================================================

    /**
     * Get the cache file path for a key.
     */
    protected function getCacheFile(string $key): string
    {
        $hash = md5($key);
        return $this->_cachedir . '/' . $hash . '.cache';
    }

    // =========================================================================
    // PUBLIC METHODS
    // =========================================================================

    /**
     * Garbage collection - remove expired cache files.
     */
    public function gc(): int
    {
        $removed = 0;
        $files = glob($this->_cachedir . '/*.cache');

        if ($files) {
            foreach ($files as $file) {
                $data = @file_get_contents($file);
                if ($data === false) {
                    continue;
                }

                $cache = @unserialize($data);
                if ($cache === false) {
                    @unlink($file);
                    $removed++;
                    continue;
                }

                if ($cache['expires'] > 0 && $cache['expires'] < time()) {
                    @unlink($file);
                    $removed++;
                }
            }
        }

        return $removed;
    }
}
