<?php

declare(strict_types=1);

namespace VCL\UI\Enums;

/**
 * Text/content alignment within a control
 */
enum Anchors: string
{
    case None = 'agNone';
    case Left = 'agLeft';
    case Center = 'agCenter';
    case Right = 'agRight';

    /**
     * Get CSS text-align value
     */
    public function toCss(): string
    {
        return match ($this) {
            self::None, self::Left => 'left',
            self::Center => 'center',
            self::Right => 'right',
        };
    }
}
