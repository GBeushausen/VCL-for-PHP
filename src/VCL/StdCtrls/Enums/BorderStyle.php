<?php

declare(strict_types=1);

namespace VCL\StdCtrls\Enums;

/**
 * BorderStyle enum for control border styles.
 */
enum BorderStyle: string
{
    case None = 'bsNone';
    case Single = 'bsSingle';

    public function toCss(): string
    {
        return match($this) {
            self::None => 'border-width: 0px; border-style: none;',
            self::Single => '',
        };
    }
}
