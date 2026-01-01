<?php

declare(strict_types=1);

namespace VCL\UI\Enums;

/**
 * Control alignment within parent container
 */
enum Alignment: string
{
    case None = 'alNone';
    case Top = 'alTop';
    case Bottom = 'alBottom';
    case Left = 'alLeft';
    case Right = 'alRight';
    case Client = 'alClient';
    case Custom = 'alCustom';

    /**
     * Get CSS value for this alignment
     */
    public function toCss(): string
    {
        return match ($this) {
            self::None => 'static',
            self::Top => 'absolute; top: 0; left: 0; right: 0',
            self::Bottom => 'absolute; bottom: 0; left: 0; right: 0',
            self::Left => 'absolute; top: 0; bottom: 0; left: 0',
            self::Right => 'absolute; top: 0; bottom: 0; right: 0',
            self::Client => 'absolute; top: 0; bottom: 0; left: 0; right: 0',
            self::Custom => 'absolute',
        };
    }
}
