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

use VCL\Core\Component;

/**
 * Abstract base class for cache implementations.
 *
 * Provides control output caching functionality. Inherit from this class
 * and implement the storage methods for your preferred caching backend.
 *
 * Example usage:
 * ```php
 * $cache = new FileCache($this);
 * $cache->CacheDir = '/tmp/vcl_cache';
 *
 * // In a control's render method:
 * if (!$cache->startCache($this, 'content')) {
 *     // Render expensive content here
 *     echo $this->renderExpensiveContent();
 *     $cache->endCache();
 * }
 * ```
 */
abstract class Cache extends Component implements CacheInterface
{
    protected ?string $currentKey = null;
    protected int $defaultTTL = 3600;

    // =========================================================================
    // PROPERTY HOOKS
    // =========================================================================

    public int $DefaultTTL {
        get => $this->defaultTTL;
        set => $this->defaultTTL = $value;
    }

    // =========================================================================
    // CacheInterface IMPLEMENTATION
    // =========================================================================

    /**
     * Start caching output for a control.
     *
     * Creates a unique key from control and cache type.
     * If cached content exists, outputs it and returns true.
     * Otherwise, starts output buffering and returns false.
     */
    public function startCache(object $control, string $cacheType): bool
    {
        $this->currentKey = $this->generateKey($control, $cacheType);

        $cached = $this->get($this->currentKey);
        if ($cached !== null) {
            echo $cached;
            return true;
        }

        ob_start();
        return false;
    }

    /**
     * Finish the caching process.
     *
     * Captures buffered output and stores it in cache.
     */
    public function endCache(): void
    {
        if ($this->currentKey === null) {
            return;
        }

        $content = ob_get_contents();
        ob_end_flush();

        $this->set($this->currentKey, $content, $this->defaultTTL);
        $this->currentKey = null;
    }

    // =========================================================================
    // PROTECTED METHODS
    // =========================================================================

    /**
     * Generate a unique cache key for a control.
     *
     * @param object $control The control being cached
     * @param string $cacheType Cache type prefix
     * @return string Cache key
     */
    protected function generateKey(object $control, string $cacheType): string
    {
        $className = $control::class;
        $name = $control->Name ?? spl_object_id($control);

        return $cacheType . '_' . $className . '_' . $name;
    }
}
