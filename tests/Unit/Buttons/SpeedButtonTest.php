<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\Buttons;

use PHPUnit\Framework\TestCase;
use VCL\Buttons\SpeedButton;

class SpeedButtonTest extends TestCase
{
    private SpeedButton $btn;

    protected function setUp(): void
    {
        $this->btn = new SpeedButton();
        $this->btn->Name = 'TestSpeedButton';
    }

    public function testDefaultDimensions(): void
    {
        $this->assertSame(25, $this->btn->Width);
        $this->assertSame(25, $this->btn->Height);
    }

    public function testDefaultAllowAllUp(): void
    {
        $this->assertFalse($this->btn->AllowAllUp);
    }

    public function testAllowAllUpProperty(): void
    {
        $this->btn->AllowAllUp = true;
        $this->assertTrue($this->btn->AllowAllUp);
    }

    public function testDefaultDown(): void
    {
        $this->assertFalse($this->btn->Down);
    }

    public function testDownProperty(): void
    {
        $this->btn->Down = true;
        $this->assertTrue($this->btn->Down);
    }

    public function testDefaultFlat(): void
    {
        $this->assertFalse($this->btn->Flat);
    }

    public function testFlatProperty(): void
    {
        $this->btn->Flat = true;
        $this->assertTrue($this->btn->Flat);
    }

    public function testDefaultGroupIndex(): void
    {
        $this->assertSame(0, $this->btn->GroupIndex);
    }

    public function testGroupIndexProperty(): void
    {
        $this->btn->GroupIndex = 1;
        $this->assertSame(1, $this->btn->GroupIndex);
    }

    public function testGroupIndexRejectsNegative(): void
    {
        $this->btn->GroupIndex = -5;
        $this->assertSame(0, $this->btn->GroupIndex);
    }

    public function testExtendsBitBtn(): void
    {
        $this->assertInstanceOf(\VCL\Buttons\BitBtn::class, $this->btn);
    }
}
