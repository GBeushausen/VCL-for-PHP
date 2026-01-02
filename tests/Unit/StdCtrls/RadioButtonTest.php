<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\StdCtrls;

use PHPUnit\Framework\TestCase;
use VCL\StdCtrls\RadioButton;

class RadioButtonTest extends TestCase
{
    private RadioButton $radio;

    protected function setUp(): void
    {
        $this->radio = new RadioButton();
        $this->radio->Name = 'TestRadio';
    }

    public function testCaptionProperty(): void
    {
        $this->radio->Caption = 'Option 1';
        $this->assertSame('Option 1', $this->radio->Caption);
    }

    public function testCheckedProperty(): void
    {
        $this->radio->Checked = true;
        $this->assertTrue($this->radio->Checked);
    }

    public function testGroupProperty(): void
    {
        $this->radio->Group = 'options';
        $this->assertSame('options', $this->radio->Group);
    }

    public function testDefaultCheckedIsFalse(): void
    {
        $this->assertFalse($this->radio->Checked);
    }
}
