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

namespace VCL\Graphics;

use VCL\Core\Component;

/**
 * A component that holds a list of image paths.
 *
 * Unlike the VCL for Windows TImageList which stores actual images,
 * this component stores paths to image files since web applications
 * reference images by URL.
 *
 * Example usage:
 * ```php
 * $imageList = new ImageList($this);
 * $imageList->Name = 'ImageList1';
 * $imageList->Images = [
 *     'icon_home' => '/images/home.png',
 *     'icon_save' => '/images/save.png',
 *     'icon_edit' => '/images/edit.png',
 * ];
 *
 * // Get an image by key
 * $homeIcon = $imageList->getImage('icon_home');
 * ```
 */
class ImageList extends Component
{
    protected array $_images = [];

    // =========================================================================
    // PROPERTY HOOKS
    // =========================================================================

    /**
     * Array of image paths this ImageList holds.
     */
    public array $Images {
        get => $this->_images;
        set => $this->_images = $value;
    }

    /**
     * Number of images in the list.
     */
    public int $Count {
        get => count($this->_images);
    }

    // =========================================================================
    // CONSTRUCTOR
    // =========================================================================

    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);
    }

    // =========================================================================
    // PUBLIC METHODS
    // =========================================================================

    /**
     * Returns an image from the array specified by index/key.
     *
     * @param string|int $index The key or index of the image
     * @return string|null The image path or null if not found
     */
    public function getImage(string|int $index): ?string
    {
        if (isset($this->_images[$index])) {
            return $this->_images[$index];
        }

        // Search by loose comparison for legacy compatibility
        foreach ($this->_images as $key => $val) {
            if ($key == $index) {
                return $val;
            }
        }

        return null;
    }

    /**
     * Returns an image from the array by ID, optionally formatted for JavaScript.
     *
     * @param string|int $index Index of the image to get
     * @param bool $preformat If true, the path will be formatted for JavaScript
     * @return string The image path (possibly formatted)
     */
    public function getImageByID(string|int $index, bool $preformat = false): string
    {
        $image = '';

        foreach ($this->_images as $k => $img) {
            if ($k == $index) {
                $image = $img;
                break;
            }
        }

        // Replace VCL path placeholder if defined
        if ($image !== '' && defined('VCL_HTTP_PATH')) {
            $image = str_replace('%VCL_HTTP_PATH%', VCL_HTTP_PATH, $image);
        }

        if ($preformat) {
            return ($image === '' || $image === null) ? 'null' : '"' . $image . '"';
        }

        return $image;
    }

    /**
     * Add an image to the list.
     *
     * @param string $path The image path
     * @param string|int|null $key Optional key for the image
     * @return int The new count of images
     */
    public function addImage(string $path, string|int|null $key = null): int
    {
        if ($key !== null) {
            $this->_images[$key] = $path;
        } else {
            $this->_images[] = $path;
        }
        return $this->Count;
    }

    /**
     * Remove an image from the list.
     *
     * @param string|int $key The key of the image to remove
     * @return bool True if the image was removed
     */
    public function removeImage(string|int $key): bool
    {
        if (isset($this->_images[$key])) {
            unset($this->_images[$key]);
            return true;
        }
        return false;
    }

    /**
     * Clear all images from the list.
     */
    public function clear(): void
    {
        $this->_images = [];
    }

    /**
     * Check if an image exists in the list.
     *
     * @param string|int $key The key to check
     * @return bool True if the image exists
     */
    public function hasImage(string|int $key): bool
    {
        return isset($this->_images[$key]);
    }

    /**
     * Get all image keys.
     *
     * @return array Array of keys
     */
    public function getKeys(): array
    {
        return array_keys($this->_images);
    }

    // =========================================================================
    // DEFAULT VALUE METHODS
    // =========================================================================

    protected function defaultImages(): array
    {
        return [];
    }
}
