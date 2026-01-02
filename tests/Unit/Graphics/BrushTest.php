<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\Graphics;

use PHPUnit\Framework\TestCase;
use VCL\Graphics\Brush;

class BrushTest extends TestCase
{
    private Brush $brush;

    protected function setUp(): void
    {
        $this->brush = new Brush();
    }

    public function testDefaultColor(): void
    {
        $this->assertSame('#FFFFFF', $this->brush->Color);
    }

    public function testColorProperty(): void
    {
        $this->brush->Color = '#FF0000';
        $this->assertSame('#FF0000', $this->brush->Color);
    }

    public function testModifiedAfterColorChange(): void
    {
        $this->brush->resetModified();
        $this->assertFalse($this->brush->isModified());
        $this->brush->Color = '#00FF00';
        $this->assertTrue($this->brush->isModified());
    }

    public function testResetModified(): void
    {
        $this->brush->Color = '#0000FF';
        $this->brush->resetModified();
        $this->assertFalse($this->brush->isModified());
    }

    public function testAssignTo(): void
    {
        $this->brush->Color = '#123456';
        $destBrush = new Brush();
        $this->brush->assignTo($destBrush);
        $this->assertSame('#123456', $destBrush->Color);
    }

    public function testIsPersistent(): void
    {
        $this->assertInstanceOf(\VCL\Core\Persistent::class, $this->brush);
    }
}
