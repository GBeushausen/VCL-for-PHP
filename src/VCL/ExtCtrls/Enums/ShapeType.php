<?php

declare(strict_types=1);

namespace VCL\ExtCtrls\Enums;

/**
 * Shape type enumeration for Shape control.
 */
enum ShapeType: string
{
    case Rectangle = 'stRectangle';
    case Square = 'stSquare';
    case RoundRect = 'stRoundRect';
    case RoundSquare = 'stRoundSquare';
    case Ellipse = 'stEllipse';
    case Circle = 'stCircle';
}
