<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\ExtCtrls;

use PHPUnit\Framework\TestCase;
use VCL\ExtCtrls\Bevel;
use VCL\ExtCtrls\Enums\BevelShape;
use VCL\ExtCtrls\Enums\BevelStyle;

class BevelTest extends TestCase
{
    private Bevel $bevel;

    protected function setUp(): void
    {
        $this->bevel = new Bevel();
        $this->bevel->Name = 'TestBevel';
    }

    public function testDefaultShape(): void
    {
        $this->assertEquals(BevelShape::Box, $this->bevel->Shape);
    }

    public function testShapeProperty(): void
    {
        $this->bevel->Shape = BevelShape::Frame;
        $this->assertEquals(BevelShape::Frame, $this->bevel->Shape);
    }

    public function testShapePropertyWithString(): void
    {
        $this->bevel->Shape = 'bsTopLine';
        $this->assertEquals(BevelShape::TopLine, $this->bevel->Shape);
    }

    public function testDefaultBevelStyle(): void
    {
        $this->assertEquals(BevelStyle::Lowered, $this->bevel->BevelStyle);
    }

    public function testBevelStyleProperty(): void
    {
        $this->bevel->BevelStyle = BevelStyle::Raised;
        $this->assertEquals(BevelStyle::Raised, $this->bevel->BevelStyle);
    }

    public function testHasCanvas(): void
    {
        $this->assertNotNull($this->bevel->_canvas);
    }

    public function testIsGraphicControl(): void
    {
        $this->assertInstanceOf(\VCL\UI\GraphicControl::class, $this->bevel);
    }
}
