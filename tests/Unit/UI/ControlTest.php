<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\UI;

use PHPUnit\Framework\TestCase;
use VCL\UI\Control;

class ControlTest extends TestCase
{
    private Control $control;

    protected function setUp(): void
    {
        $this->control = new Control();
        $this->control->Name = 'TestControl';
    }

    public function testLeftProperty(): void
    {
        $this->control->Left = 100;
        $this->assertSame(100, $this->control->Left);
    }

    public function testTopProperty(): void
    {
        $this->control->Top = 50;
        $this->assertSame(50, $this->control->Top);
    }

    public function testWidthProperty(): void
    {
        $this->control->Width = 200;
        $this->assertSame(200, $this->control->Width);
    }

    public function testHeightProperty(): void
    {
        $this->control->Height = 150;
        $this->assertSame(150, $this->control->Height);
    }

    public function testCaptionProperty(): void
    {
        $this->control->Caption = 'Test Caption';
        $this->assertSame('Test Caption', $this->control->Caption);
    }

    public function testColorProperty(): void
    {
        $this->control->Color = '#FF0000';
        $this->assertSame('#FF0000', $this->control->Color);
    }

    public function testHintProperty(): void
    {
        $this->control->Hint = 'This is a hint';
        $this->assertSame('This is a hint', $this->control->Hint);
    }

    public function testDefaultVisibleIsTrue(): void
    {
        $this->assertTrue($this->control->Visible);
    }

    public function testVisibleProperty(): void
    {
        $this->control->Visible = false;
        $this->assertFalse($this->control->Visible);
    }

    public function testDefaultEnabledIsTrue(): void
    {
        $this->assertTrue($this->control->Enabled);
    }

    public function testEnabledProperty(): void
    {
        $this->control->Enabled = false;
        $this->assertFalse($this->control->Enabled);
    }

    public function testShowHintProperty(): void
    {
        $this->control->ShowHint = true;
        $this->assertTrue($this->control->ShowHint);
    }

    public function testStyleProperty(): void
    {
        $this->control->Style = '.myStyle';
        $this->assertSame('.myStyle', $this->control->Style);
    }

    public function testFontProperty(): void
    {
        $font = $this->control->Font;
        $this->assertInstanceOf(\VCL\Graphics\Font::class, $font);
    }

    public function testIsComponent(): void
    {
        $this->assertInstanceOf(\VCL\Core\Component::class, $this->control);
    }
}
