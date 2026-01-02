<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\Graphics;

use PHPUnit\Framework\TestCase;
use VCL\Graphics\Layout;
use VCL\Graphics\Enums\LayoutType;

class LayoutTest extends TestCase
{
    private Layout $layout;

    protected function setUp(): void
    {
        $this->layout = new Layout();
    }

    public function testDefaultType(): void
    {
        $this->assertEquals(LayoutType::AbsXY, $this->layout->Type);
    }

    public function testTypePropertyWithEnum(): void
    {
        $this->layout->Type = LayoutType::Flow;
        $this->assertEquals(LayoutType::Flow, $this->layout->Type);
    }

    public function testDefaultRows(): void
    {
        $this->assertSame(5, $this->layout->Rows);
    }

    public function testRowsProperty(): void
    {
        $this->layout->Rows = 10;
        $this->assertSame(10, $this->layout->Rows);
    }

    public function testRowsMinimumIsOne(): void
    {
        $this->layout->Rows = 0;
        $this->assertSame(1, $this->layout->Rows);
    }

    public function testDefaultCols(): void
    {
        $this->assertSame(5, $this->layout->Cols);
    }

    public function testColsProperty(): void
    {
        $this->layout->Cols = 8;
        $this->assertSame(8, $this->layout->Cols);
    }

    public function testColsMinimumIsOne(): void
    {
        $this->layout->Cols = 0;
        $this->assertSame(1, $this->layout->Cols);
    }

    public function testDefaultUsePixelTrans(): void
    {
        $this->assertTrue($this->layout->UsePixelTrans);
    }

    public function testUsePixelTransProperty(): void
    {
        $this->layout->UsePixelTrans = false;
        $this->assertFalse($this->layout->UsePixelTrans);
    }

    public function testReadOwnerReturnsNull(): void
    {
        $this->assertNull($this->layout->readOwner());
    }

    public function testReadOwnerReturnsControl(): void
    {
        $mockControl = new \stdClass();
        $this->layout->_control = $mockControl;

        $this->assertSame($mockControl, $this->layout->readOwner());
    }

    public function testIsPersistent(): void
    {
        $this->assertInstanceOf(\VCL\Core\Persistent::class, $this->layout);
    }
}
