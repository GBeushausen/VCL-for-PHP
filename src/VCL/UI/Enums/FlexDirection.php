<?php
/**
 * VCL for PHP 3.0
 *
 * Flex direction enum for FlexPanel
 */

declare(strict_types=1);

namespace VCL\UI\Enums;

/**
 * Defines the direction of flex items in a FlexPanel.
 */
enum FlexDirection: string
{
    case Row = 'row';
    case RowReverse = 'row-reverse';
    case Column = 'col';
    case ColumnReverse = 'col-reverse';

    /**
     * Get the Tailwind CSS class for this direction.
     */
    public function toTailwind(): string
    {
        return match ($this) {
            self::Row => 'flex-row',
            self::RowReverse => 'flex-row-reverse',
            self::Column => 'flex-col',
            self::ColumnReverse => 'flex-col-reverse',
        };
    }

    /**
     * Check if this direction is horizontal (row-based).
     */
    public function isHorizontal(): bool
    {
        return $this === self::Row || $this === self::RowReverse;
    }

    /**
     * Check if this direction is vertical (column-based).
     */
    public function isVertical(): bool
    {
        return $this === self::Column || $this === self::ColumnReverse;
    }
}
