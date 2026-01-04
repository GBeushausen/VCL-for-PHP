<?php
/**
 * VCL for PHP 3.0
 *
 * Align items enum for flex/grid layouts
 */

declare(strict_types=1);

namespace VCL\UI\Enums;

/**
 * Defines how items are aligned along the cross axis in flex/grid containers.
 */
enum AlignItems: string
{
    case Start = 'start';
    case End = 'end';
    case Center = 'center';
    case Baseline = 'baseline';
    case Stretch = 'stretch';

    /**
     * Get the Tailwind CSS class for this alignment.
     */
    public function toTailwind(): string
    {
        return match ($this) {
            self::Start => 'items-start',
            self::End => 'items-end',
            self::Center => 'items-center',
            self::Baseline => 'items-baseline',
            self::Stretch => 'items-stretch',
        };
    }
}
