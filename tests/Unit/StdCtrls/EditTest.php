<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\StdCtrls;

use PHPUnit\Framework\TestCase;
use VCL\StdCtrls\Edit;

class EditTest extends TestCase
{
    private Edit $edit;

    protected function setUp(): void
    {
        $this->edit = new Edit();
        $this->edit->Name = 'TestEdit';
    }

    public function testTextProperty(): void
    {
        $this->edit->Text = 'Hello World';
        $this->assertSame('Hello World', $this->edit->Text);
    }

    public function testMaxLengthProperty(): void
    {
        $this->edit->MaxLength = 100;
        $this->assertSame(100, $this->edit->MaxLength);
    }

    public function testReadOnlyProperty(): void
    {
        $this->edit->ReadOnly = true;
        $this->assertTrue($this->edit->ReadOnly);
    }

    public function testIsPasswordProperty(): void
    {
        $this->edit->IsPassword = true;
        $this->assertTrue($this->edit->IsPassword);
    }

    public function testDefaultWidth(): void
    {
        $this->assertSame(121, $this->edit->Width);
    }

    public function testDefaultHeight(): void
    {
        $this->assertSame(21, $this->edit->Height);
    }

    public function testTabStopProperty(): void
    {
        $this->edit->TabStop = false;
        $this->assertFalse($this->edit->TabStop);
    }

    public function testTabOrderProperty(): void
    {
        $this->edit->TabOrder = 5;
        $this->assertSame(5, $this->edit->TabOrder);
    }
}
