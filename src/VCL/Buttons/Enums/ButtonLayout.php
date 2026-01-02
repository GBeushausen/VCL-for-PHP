<?php

declare(strict_types=1);

namespace VCL\Buttons\Enums;

/**
 * Button layout enumeration.
 *
 * Specifies the position where the image appears on the bitmap button.
 */
enum ButtonLayout: string
{
    case ImageBottom = 'blImageBottom';
    case ImageLeft = 'blImageLeft';
    case ImageRight = 'blImageRight';
    case ImageTop = 'blImageTop';
}
