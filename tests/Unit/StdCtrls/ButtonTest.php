<?php
/**
 * VCL for PHP 3.0
 *
 * Unit tests for Button class
 */

declare(strict_types=1);

namespace VCL\Tests\Unit\StdCtrls;

use PHPUnit\Framework\TestCase;
use VCL\StdCtrls\Button;

class ButtonTest extends TestCase
{
    private Button $button;

    protected function setUp(): void
    {
        $this->button = new Button();
        $this->button->Name = 'TestButton';
    }

    public function testCaptionProperty(): void
    {
        $this->button->Caption = 'Click Me';
        $this->assertSame('Click Me', $this->button->Caption);
    }

    public function testDefaultWidth(): void
    {
        $this->assertSame(75, $this->button->Width);
    }

    public function testDefaultHeight(): void
    {
        $this->assertSame(25, $this->button->Height);
    }

    public function testEnabledDefaultsToTrue(): void
    {
        $this->assertTrue($this->button->Enabled);
    }

    public function testVisibleDefaultsToTrue(): void
    {
        $this->assertTrue($this->button->Visible);
    }

    public function testTabStopDefaultsToTrue(): void
    {
        $this->assertTrue($this->button->TabStop);
    }

    public function testButtonTypeProperty(): void
    {
        $this->button->ButtonType = 'btSubmit';
        $this->assertSame('btSubmit', $this->button->ButtonType);
    }
}
