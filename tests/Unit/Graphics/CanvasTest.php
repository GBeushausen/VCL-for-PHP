<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\Graphics;

use PHPUnit\Framework\TestCase;
use VCL\Graphics\Canvas;
use VCL\Graphics\Pen;
use VCL\Graphics\Brush;
use VCL\Graphics\Font;

class CanvasTest extends TestCase
{
    private Canvas $canvas;

    protected function setUp(): void
    {
        $this->canvas = new Canvas();
    }

    public function testPenProperty(): void
    {
        $pen = $this->canvas->Pen;
        $this->assertInstanceOf(Pen::class, $pen);
    }

    public function testBrushProperty(): void
    {
        $brush = $this->canvas->Brush;
        $this->assertInstanceOf(Brush::class, $brush);
    }

    public function testFontProperty(): void
    {
        $font = $this->canvas->Font;
        $this->assertInstanceOf(Font::class, $font);
    }

    public function testSetPen(): void
    {
        $newPen = new Pen();
        $newPen->Color = '#FF0000';
        $this->canvas->Pen = $newPen;

        $this->assertSame('#FF0000', $this->canvas->Pen->Color);
    }

    public function testSetBrush(): void
    {
        $newBrush = new Brush();
        $newBrush->Color = '#00FF00';
        $this->canvas->Brush = $newBrush;

        $this->assertSame('#00FF00', $this->canvas->Brush->Color);
    }

    public function testSetFont(): void
    {
        $newFont = new Font();
        $newFont->Family = 'Arial';
        $this->canvas->Font = $newFont;

        $this->assertSame('Arial', $this->canvas->Font->Family);
    }

    public function testSetCanvasProperties(): void
    {
        $this->canvas->setCanvasProperties('MyControl');
        // Verify no error is thrown
        $this->assertTrue(true);
    }

    public function testLineGeneratesJavaScript(): void
    {
        $this->canvas->setCanvasProperties('TestCanvas');

        ob_start();
        $this->canvas->beginDraw();
        $this->canvas->line(0, 0, 100, 100);
        $this->canvas->endDraw();
        $output = ob_get_clean();

        $this->assertStringContainsString('drawLine', $output);
        $this->assertStringContainsString('0, 0, 100, 100', $output);
    }

    public function testRectangleGeneratesJavaScript(): void
    {
        $this->canvas->setCanvasProperties('TestCanvas');

        ob_start();
        $this->canvas->beginDraw();
        $this->canvas->rectangle(10, 10, 50, 50);
        $this->canvas->endDraw();
        $output = ob_get_clean();

        $this->assertStringContainsString('Rect', $output);
    }

    public function testEllipseGeneratesJavaScript(): void
    {
        $this->canvas->setCanvasProperties('TestCanvas');

        ob_start();
        $this->canvas->beginDraw();
        $this->canvas->ellipse(10, 10, 100, 80);
        $this->canvas->endDraw();
        $output = ob_get_clean();

        $this->assertStringContainsString('Ellipse', $output);
    }

    public function testPolygonGeneratesJavaScript(): void
    {
        $this->canvas->setCanvasProperties('TestCanvas');

        ob_start();
        $this->canvas->beginDraw();
        $this->canvas->polygon([0, 0, 50, 0, 25, 50]);
        $this->canvas->endDraw();
        $output = ob_get_clean();

        $this->assertStringContainsString('Polygon', $output);
    }

    public function testTextOutGeneratesJavaScript(): void
    {
        $this->canvas->setCanvasProperties('TestCanvas');

        ob_start();
        $this->canvas->beginDraw();
        $this->canvas->textOut(10, 10, 'Hello World');
        $this->canvas->endDraw();
        $output = ob_get_clean();

        $this->assertStringContainsString('drawString', $output);
        $this->assertStringContainsString('Hello World', $output);
    }

    public function testClearGeneratesJavaScript(): void
    {
        $this->canvas->setCanvasProperties('TestCanvas');

        ob_start();
        $this->canvas->beginDraw();
        $this->canvas->clear();
        $this->canvas->endDraw();
        $output = ob_get_clean();

        $this->assertStringContainsString('clear()', $output);
    }

    public function testIsPersistent(): void
    {
        $this->assertInstanceOf(\VCL\Core\Persistent::class, $this->canvas);
    }
}
