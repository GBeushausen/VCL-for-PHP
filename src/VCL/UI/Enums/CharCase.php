<?php

declare(strict_types=1);

namespace VCL\UI\Enums;

/**
 * Edit control character case transformation
 */
enum CharCase: string
{
    case Normal = 'ecNormal';
    case LowerCase = 'ecLowerCase';
    case UpperCase = 'ecUpperCase';

    /**
     * Get CSS text-transform value
     */
    public function toCss(): string
    {
        return match ($this) {
            self::Normal => 'none',
            self::LowerCase => 'lowercase',
            self::UpperCase => 'uppercase',
        };
    }

    /**
     * Transform a string according to this case
     */
    public function transform(string $value): string
    {
        return match ($this) {
            self::Normal => $value,
            self::LowerCase => mb_strtolower($value),
            self::UpperCase => mb_strtoupper($value),
        };
    }
}
