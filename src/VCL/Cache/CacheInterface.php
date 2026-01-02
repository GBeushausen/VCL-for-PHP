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
 * Interface for cache implementations.
 *
 * Implement this interface to create cache providers that can store
 * and retrieve rendered component output.
 */
interface CacheInterface
{
    /**
     * Start caching output for a control.
     *
     * If cached content exists, it should be output and true returned.
     * If not cached, output buffering should start and false returned.
     *
     * @param object $control Control to be cached
     * @param string $cacheType A prefix to specify what kind of contents are being cached
     * @return bool True if content was already cached (and output), false if new cache started
     */
    public function startCache(object $control, string $cacheType): bool;

    /**
     * Finish the caching process.
     *
     * Should stop output buffering and store the captured content.
     */
    public function endCache(): void;

    /**
     * Get cached content for a key.
     *
     * @param string $key Cache key
     * @return string|null Cached content or null if not found
     */
    public function get(string $key): ?string;

    /**
     * Set cached content for a key.
     *
     * @param string $key Cache key
     * @param string $content Content to cache
     * @param int $ttl Time to live in seconds (0 = forever)
     */
    public function set(string $key, string $content, int $ttl = 0): void;

    /**
     * Check if a key exists in cache.
     *
     * @param string $key Cache key
     * @return bool True if cached
     */
    public function has(string $key): bool;

    /**
     * Delete a cached item.
     *
     * @param string $key Cache key
     */
    public function delete(string $key): void;

    /**
     * Clear all cached items.
     */
    public function clear(): void;
}
