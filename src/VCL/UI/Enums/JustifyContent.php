<?php
/**
 * VCL for PHP 3.0
 *
 * Justify content enum for flex/grid layouts
 */

declare(strict_types=1);

namespace VCL\UI\Enums;

/**
 * Defines how items are distributed along the main axis in flex/grid containers.
 */
enum JustifyContent: string
{
    case Start = 'start';
    case End = 'end';
    case Center = 'center';
    case Between = 'between';
    case Around = 'around';
    case Evenly = 'evenly';
    case Stretch = 'stretch';

    /**
     * Get the Tailwind CSS class for this justification.
     */
    public function toTailwind(): string
    {
        return match ($this) {
            self::Start => 'justify-start',
            self::End => 'justify-end',
            self::Center => 'justify-center',
            self::Between => 'justify-between',
            self::Around => 'justify-around',
            self::Evenly => 'justify-evenly',
            self::Stretch => 'justify-stretch',
        };
    }
}
