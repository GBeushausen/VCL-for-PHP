<?php

declare(strict_types=1);

namespace VCL\ExtCtrls\Enums;

/**
 * Bevel shape enumeration.
 */
enum BevelShape: string
{
    case Box = 'bsBox';
    case Frame = 'bsFrame';
    case TopLine = 'bsTopLine';
    case BottomLine = 'bsBottomLine';
    case LeftLine = 'bsLeftLine';
    case RightLine = 'bsRightLine';
    case Spacer = 'bsSpacer';
}
