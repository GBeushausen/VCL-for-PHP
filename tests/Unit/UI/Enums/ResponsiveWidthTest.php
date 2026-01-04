<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\UI\Enums;

use PHPUnit\Framework\TestCase;
use VCL\UI\Enums\ResponsiveWidth;

class ResponsiveWidthTest extends TestCase
{
    public function testToTailwind(): void
    {
        $this->assertSame('w-full', ResponsiveWidth::Full->toTailwind());
        $this->assertSame('w-auto', ResponsiveWidth::Auto->toTailwind());
        $this->assertSame('w-1/2', ResponsiveWidth::Half->toTailwind());
        $this->assertSame('w-1/3', ResponsiveWidth::Third->toTailwind());
        $this->assertSame('w-2/3', ResponsiveWidth::TwoThirds->toTailwind());
        $this->assertSame('w-1/4', ResponsiveWidth::Quarter->toTailwind());
    }

    public function testToTailwindWithBreakpoint(): void
    {
        $this->assertSame('sm:w-full', ResponsiveWidth::Full->toTailwindWithBreakpoint('sm'));
        $this->assertSame('md:w-1/2', ResponsiveWidth::Half->toTailwindWithBreakpoint('md'));
        $this->assertSame('lg:w-auto', ResponsiveWidth::Auto->toTailwindWithBreakpoint('lg'));
    }

    public function testBuildResponsiveClasses(): void
    {
        $classes = ResponsiveWidth::buildResponsiveClasses(
            ResponsiveWidth::Full,
            [
                'sm' => ResponsiveWidth::Half,
                'md' => ResponsiveWidth::Third,
            ]
        );

        $this->assertSame('w-full sm:w-1/2 md:w-1/3', $classes);
    }
}
