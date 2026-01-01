<?php

declare(strict_types=1);

namespace VCL\Graphics\Enums;

/**
 * Text alignment options.
 */
enum TextAlign: string
{
    case None = 'taNone';
    case Left = 'taLeft';
    case Center = 'taCenter';
    case Right = 'taRight';
    case Justify = 'taJustify';

    public function toCss(): string
    {
        return match($this) {
            self::None => '',
            self::Left => 'text-align: left;',
            self::Center => 'text-align: center;',
            self::Right => 'text-align: right;',
            self::Justify => 'text-align: justify;',
        };
    }
}
