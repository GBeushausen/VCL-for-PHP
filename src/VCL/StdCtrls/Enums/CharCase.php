<?php

declare(strict_types=1);

namespace VCL\StdCtrls\Enums;

/**
 * CharCase enum for text input character case transformation.
 */
enum CharCase: string
{
    case Normal = 'ecNormal';
    case LowerCase = 'ecLowerCase';
    case UpperCase = 'ecUpperCase';

    public function toCss(): string
    {
        return match($this) {
            self::Normal => '',
            self::LowerCase => 'text-transform: lowercase;',
            self::UpperCase => 'text-transform: uppercase;',
        };
    }
}
