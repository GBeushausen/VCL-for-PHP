<?php

declare(strict_types=1);

namespace VCL\Graphics\Enums;

/**
 * Font variant options.
 */
enum FontVariant: string
{
    case Normal = 'vaNormal';
    case SmallCaps = 'vaSmallCaps';

    public function toCss(): string
    {
        return match($this) {
            self::Normal => 'font-variant: normal;',
            self::SmallCaps => 'font-variant: small-caps;',
        };
    }
}
