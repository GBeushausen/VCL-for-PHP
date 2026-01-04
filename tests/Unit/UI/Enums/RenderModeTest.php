<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\UI\Enums;

use PHPUnit\Framework\TestCase;
use VCL\UI\Enums\RenderMode;

class RenderModeTest extends TestCase
{
    public function testEnumValues(): void
    {
        $this->assertSame('classic', RenderMode::Classic->value);
        $this->assertSame('tailwind', RenderMode::Tailwind->value);
        $this->assertSame('hybrid', RenderMode::Hybrid->value);
    }

    public function testUsesTailwind(): void
    {
        $this->assertFalse(RenderMode::Classic->usesTailwind());
        $this->assertTrue(RenderMode::Tailwind->usesTailwind());
        $this->assertTrue(RenderMode::Hybrid->usesTailwind());
    }

    public function testUsesInlinePositioning(): void
    {
        $this->assertTrue(RenderMode::Classic->usesInlinePositioning());
        $this->assertFalse(RenderMode::Tailwind->usesInlinePositioning());
        $this->assertTrue(RenderMode::Hybrid->usesInlinePositioning());
    }

    public function testUsesInlineAppearance(): void
    {
        $this->assertTrue(RenderMode::Classic->usesInlineAppearance());
        $this->assertFalse(RenderMode::Tailwind->usesInlineAppearance());
        $this->assertFalse(RenderMode::Hybrid->usesInlineAppearance());
    }
}
