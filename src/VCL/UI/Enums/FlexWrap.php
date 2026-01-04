<?php
/**
 * VCL for PHP 3.0
 *
 * Flex wrap enum for FlexPanel
 */

declare(strict_types=1);

namespace VCL\UI\Enums;

/**
 * Defines whether flex items wrap in a FlexPanel.
 */
enum FlexWrap: string
{
    case NoWrap = 'nowrap';
    case Wrap = 'wrap';
    case WrapReverse = 'wrap-reverse';

    /**
     * Get the Tailwind CSS class for this wrap mode.
     */
    public function toTailwind(): string
    {
        return match ($this) {
            self::NoWrap => 'flex-nowrap',
            self::Wrap => 'flex-wrap',
            self::WrapReverse => 'flex-wrap-reverse',
        };
    }
}
