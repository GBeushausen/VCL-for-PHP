<?php

declare(strict_types=1);

namespace VCL\Forms\Enums;

/**
 * FrameBorder enum for frame border styles.
 */
enum FrameBorder: string
{
    case Default = 'fbDefault';
    case No = 'fbNo';
    case Yes = 'fbYes';

    public function toHtmlValue(): string
    {
        return match($this) {
            self::Default => '1',
            self::No => '0',
            self::Yes => '1',
        };
    }
}
