<?php
/**
 * VCL for PHP 3.0
 *
 * Render mode enum for control styling
 */

declare(strict_types=1);

namespace VCL\UI\Enums;

/**
 * Defines how controls render their styles.
 *
 * - Classic: Traditional inline styles (position, size, colors) - default
 * - Tailwind: Pure Tailwind CSS classes, minimal inline styles
 * - Hybrid: Position via inline styles, everything else via Tailwind
 */
enum RenderMode: string
{
    case Classic = 'classic';
    case Tailwind = 'tailwind';
    case Hybrid = 'hybrid';

    /**
     * Check if this mode uses Tailwind classes.
     */
    public function usesTailwind(): bool
    {
        return $this !== self::Classic;
    }

    /**
     * Check if this mode uses inline styles for positioning.
     */
    public function usesInlinePositioning(): bool
    {
        return $this !== self::Tailwind;
    }

    /**
     * Check if this mode uses inline styles for appearance (colors, fonts, etc.).
     */
    public function usesInlineAppearance(): bool
    {
        return $this === self::Classic;
    }
}
