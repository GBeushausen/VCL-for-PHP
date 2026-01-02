<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\UI;

use PHPUnit\Framework\TestCase;
use VCL\UI\GraphicControl;

class GraphicControlTest extends TestCase
{
    private GraphicControl $control;

    protected function setUp(): void
    {
        $this->control = new GraphicControl();
        $this->control->Name = 'TestGraphicControl';
    }

    public function testIsControl(): void
    {
        $this->assertInstanceOf(\VCL\UI\Control::class, $this->control);
    }

    public function testExtendsControl(): void
    {
        // GraphicControl directly extends Control (not FocusControl)
        $this->assertInstanceOf(\VCL\UI\Control::class, $this->control);
        $this->assertNotInstanceOf(\VCL\UI\FocusControl::class, $this->control);
    }

    public function testCanSetName(): void
    {
        $this->control->Name = 'TestControl';
        $this->assertSame('TestControl', $this->control->Name);
    }

    public function testCanSetDimensions(): void
    {
        $this->control->Width = 200;
        $this->control->Height = 100;

        $this->assertSame(200, $this->control->Width);
        $this->assertSame(100, $this->control->Height);
    }
}
