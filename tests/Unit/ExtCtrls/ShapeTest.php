<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\ExtCtrls;

use PHPUnit\Framework\TestCase;
use VCL\ExtCtrls\Shape;
use VCL\ExtCtrls\Enums\ShapeType;

class ShapeTest extends TestCase
{
    private Shape $shape;

    protected function setUp(): void
    {
        $this->shape = new Shape();
        $this->shape->Name = 'TestShape';
    }

    public function testDefaultDimensions(): void
    {
        $this->assertSame(65, $this->shape->Width);
        $this->assertSame(65, $this->shape->Height);
    }

    public function testDefaultShapeType(): void
    {
        $this->assertEquals(ShapeType::Rectangle, $this->shape->Shape);
    }

    public function testShapePropertyWithEnum(): void
    {
        $this->shape->Shape = ShapeType::Circle;
        $this->assertEquals(ShapeType::Circle, $this->shape->Shape);
    }

    public function testShapePropertyWithString(): void
    {
        $this->shape->Shape = 'stEllipse';
        $this->assertEquals(ShapeType::Ellipse, $this->shape->Shape);
    }

    public function testPenPropertyExists(): void
    {
        $this->assertNotNull($this->shape->Pen);
    }

    public function testBrushPropertyExists(): void
    {
        $this->assertNotNull($this->shape->Brush);
    }

    public function testPenColorChange(): void
    {
        $this->shape->Pen->Color = '#FF0000';
        $this->assertSame('#FF0000', $this->shape->Pen->Color);
    }

    public function testBrushColorChange(): void
    {
        $this->shape->Brush->Color = '#00FF00';
        $this->assertSame('#00FF00', $this->shape->Brush->Color);
    }
}
