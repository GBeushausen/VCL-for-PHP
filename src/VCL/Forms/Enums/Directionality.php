<?php

declare(strict_types=1);

namespace VCL\Forms\Enums;

/**
 * Directionality enum for text direction.
 */
enum Directionality: string
{
    case LeftToRight = 'ddLeftToRight';
    case RightToLeft = 'ddRightToLeft';

    public function toHtmlDir(): string
    {
        return match($this) {
            self::LeftToRight => 'ltr',
            self::RightToLeft => 'rtl',
        };
    }
}
