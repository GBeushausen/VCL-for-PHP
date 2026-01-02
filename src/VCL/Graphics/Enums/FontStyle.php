<?php

declare(strict_types=1);

namespace VCL\Graphics\Enums;

/**
 * Font style options.
 */
enum FontStyle: string
{
    case Normal = 'fsNormal';
    case Italic = 'fsItalic';
    case Oblique = 'fsOblique';

    public function toCss(): string
    {
        return match($this) {
            self::Normal => 'font-style: normal;',
            self::Italic => 'font-style: italic;',
            self::Oblique => 'font-style: oblique;',
        };
    }
}
