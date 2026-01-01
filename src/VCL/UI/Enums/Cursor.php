<?php

declare(strict_types=1);

namespace VCL\UI\Enums;

/**
 * Mouse cursor types
 */
enum Cursor: string
{
    case Pointer = 'crPointer';
    case CrossHair = 'crCrossHair';
    case Text = 'crText';
    case Wait = 'crWait';
    case Default = 'crDefault';
    case Help = 'crHelp';
    case EResize = 'crE-Resize';
    case NEResize = 'crNE-Resize';
    case NResize = 'crN-Resize';
    case NWResize = 'crNW-Resize';
    case WResize = 'crW-Resize';
    case SWResize = 'crSW-Resize';
    case SResize = 'crS-Resize';
    case SEResize = 'crSE-Resize';
    case Auto = 'crAuto';

    /**
     * Get CSS cursor value
     */
    public function toCss(): string
    {
        return match ($this) {
            self::Pointer => 'pointer',
            self::CrossHair => 'crosshair',
            self::Text => 'text',
            self::Wait => 'wait',
            self::Default => 'default',
            self::Help => 'help',
            self::EResize => 'e-resize',
            self::NEResize => 'ne-resize',
            self::NResize => 'n-resize',
            self::NWResize => 'nw-resize',
            self::WResize => 'w-resize',
            self::SWResize => 'sw-resize',
            self::SResize => 's-resize',
            self::SEResize => 'se-resize',
            self::Auto => 'auto',
        };
    }
}
