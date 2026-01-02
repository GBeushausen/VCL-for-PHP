<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\ExtCtrls;

use PHPUnit\Framework\TestCase;
use VCL\ExtCtrls\PaintBox;

class PaintBoxTest extends TestCase
{
    private PaintBox $paintbox;

    protected function setUp(): void
    {
        $this->paintbox = new PaintBox();
        $this->paintbox->Name = 'TestPaintBox';
    }

    public function testDefaultDimensions(): void
    {
        $this->assertSame(100, $this->paintbox->Width);
        $this->assertSame(100, $this->paintbox->Height);
    }

    public function testCanvasProperty(): void
    {
        $canvas = $this->paintbox->Canvas;
        $this->assertInstanceOf(\VCL\Graphics\Canvas::class, $canvas);
    }

    public function testOnPaintEvent(): void
    {
        $this->paintbox->OnPaint = 'handlePaint';
        $this->assertSame('handlePaint', $this->paintbox->OnPaint);
    }

    public function testOnClickEvent(): void
    {
        $this->paintbox->OnClick = 'handleClick';
        $this->assertSame('handleClick', $this->paintbox->OnClick);
    }

    public function testOnDblClickEvent(): void
    {
        $this->paintbox->OnDblClick = 'handleDblClick';
        $this->assertSame('handleDblClick', $this->paintbox->OnDblClick);
    }

    public function testIsControl(): void
    {
        $this->assertInstanceOf(\VCL\UI\Control::class, $this->paintbox);
    }
}
