<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\StdCtrls;

use PHPUnit\Framework\TestCase;
use VCL\StdCtrls\CheckBox;

class CheckBoxTest extends TestCase
{
    private CheckBox $checkbox;

    protected function setUp(): void
    {
        $this->checkbox = new CheckBox();
        $this->checkbox->Name = 'TestCheckBox';
    }

    public function testCaptionProperty(): void
    {
        $this->checkbox->Caption = 'Accept Terms';
        $this->assertSame('Accept Terms', $this->checkbox->Caption);
    }

    public function testCheckedProperty(): void
    {
        $this->checkbox->Checked = true;
        $this->assertTrue($this->checkbox->Checked);

        $this->checkbox->Checked = false;
        $this->assertFalse($this->checkbox->Checked);
    }

    public function testDefaultCheckedIsFalse(): void
    {
        $this->assertFalse($this->checkbox->Checked);
    }

    public function testEnabledProperty(): void
    {
        $this->checkbox->Enabled = false;
        $this->assertFalse($this->checkbox->Enabled);
    }
}
