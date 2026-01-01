<?php

declare(strict_types=1);

namespace VCL\Graphics\Enums;

/**
 * Pen drawing style options.
 */
enum PenStyle: string
{
    case Solid = 'psSolid';
    case Dash = 'psDash';
    case Dot = 'psDot';
    case DashDot = 'psDashDot';
    case DashDotDot = 'psDashDotDot';

    /**
     * Get the dash pattern array for this pen style.
     *
     * @return array<int> Pattern array for use with imagesetstyle()
     */
    public function getPattern(int $foreground, int $background): array
    {
        return match($this) {
            self::Solid => [$foreground],
            self::Dash => [$foreground, $foreground, $foreground, $foreground, $background, $background, $background, $background],
            self::Dot => [$foreground, $background, $background, $foreground, $background, $background],
            self::DashDot => [$foreground, $foreground, $foreground, $foreground, $background, $background, $foreground, $background, $background],
            self::DashDotDot => [$foreground, $foreground, $foreground, $foreground, $background, $foreground, $background, $foreground, $background],
        };
    }
}
