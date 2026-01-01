<?php

declare(strict_types=1);

namespace VCL\Graphics\Enums;

/**
 * Text case transformation options.
 */
enum TextCase: string
{
    case None = 'caNone';
    case Capitalize = 'caCapitalize';
    case UpperCase = 'caUpperCase';
    case LowerCase = 'caLowerCase';

    public function toCss(): string
    {
        return match($this) {
            self::None => 'text-transform: none;',
            self::Capitalize => 'text-transform: capitalize;',
            self::UpperCase => 'text-transform: uppercase;',
            self::LowerCase => 'text-transform: lowercase;',
        };
    }
}
