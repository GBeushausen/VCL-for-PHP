<?php

declare(strict_types=1);

namespace VCL\UI\Enums;

/**
 * Control border styles
 */
enum BorderStyle: string
{
    // Basic border styles (stdctrls)
    case None = 'bsNone';
    case Single = 'bsSingle';

    // Extended border styles (extctrls - Bevel)
    case Box = 'bsBox';
    case Frame = 'bsFrame';
    case TopLine = 'bsTopLine';
    case BottomLine = 'bsBottomLine';
    case LeftLine = 'bsLeftLine';
    case RightLine = 'bsRightLine';
    case Spacer = 'bsSpacer';
    case Lowered = 'bsLowered';
    case Raised = 'bsRaised';

    /**
     * Get CSS border style
     */
    public function toCss(): string
    {
        return match ($this) {
            self::None, self::Spacer => 'none',
            self::Single => 'solid 1px',
            self::Box => 'solid 1px',
            self::Frame => 'double 3px',
            self::TopLine => 'solid 1px transparent; border-top-color: currentColor',
            self::BottomLine => 'solid 1px transparent; border-bottom-color: currentColor',
            self::LeftLine => 'solid 1px transparent; border-left-color: currentColor',
            self::RightLine => 'solid 1px transparent; border-right-color: currentColor',
            self::Lowered => 'inset 2px',
            self::Raised => 'outset 2px',
        };
    }
}
