<?php

declare(strict_types=1);

namespace VCL\UI\Enums;

/**
 * HTML button types
 */
enum ButtonType: string
{
    case Submit = 'btSubmit';
    case Reset = 'btReset';
    case Normal = 'btNormal';

    /**
     * Get HTML type attribute value
     */
    public function toHtml(): string
    {
        return match ($this) {
            self::Submit => 'submit',
            self::Reset => 'reset',
            self::Normal => 'button',
        };
    }
}
