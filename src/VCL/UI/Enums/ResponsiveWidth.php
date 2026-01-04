<?php
/**
 * VCL for PHP 3.0
 *
 * Responsive width enum for Tailwind-based sizing
 */

declare(strict_types=1);

namespace VCL\UI\Enums;

/**
 * Defines responsive width values using Tailwind CSS classes.
 */
enum ResponsiveWidth: string
{
    case Auto = 'auto';
    case Full = 'full';
    case Screen = 'screen';
    case Min = 'min';
    case Max = 'max';
    case Fit = 'fit';
    case Half = '1/2';
    case Third = '1/3';
    case TwoThirds = '2/3';
    case Quarter = '1/4';
    case ThreeQuarters = '3/4';
    case Fifth = '1/5';
    case TwoFifths = '2/5';
    case ThreeFifths = '3/5';
    case FourFifths = '4/5';
    case Sixth = '1/6';
    case FiveSixths = '5/6';

    /**
     * Get the Tailwind CSS class for this width.
     */
    public function toTailwind(): string
    {
        return 'w-' . $this->value;
    }

    /**
     * Get the Tailwind CSS class with a breakpoint prefix.
     *
     * @param string $breakpoint Tailwind breakpoint (sm, md, lg, xl, 2xl)
     */
    public function toTailwindWithBreakpoint(string $breakpoint): string
    {
        return $breakpoint . ':w-' . $this->value;
    }

    /**
     * Get responsive classes for multiple breakpoints.
     *
     * @param array<string, ResponsiveWidth> $breakpoints Map of breakpoint => width
     * @return string Space-separated Tailwind classes
     */
    public static function buildResponsiveClasses(self $default, array $breakpoints = []): string
    {
        $classes = [$default->toTailwind()];

        foreach ($breakpoints as $breakpoint => $width) {
            $classes[] = $width->toTailwindWithBreakpoint($breakpoint);
        }

        return implode(' ', $classes);
    }
}
