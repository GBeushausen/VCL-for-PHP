<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\StdCtrls;

use PHPUnit\Framework\TestCase;
use VCL\StdCtrls\Memo;

class MemoTest extends TestCase
{
    private Memo $memo;

    protected function setUp(): void
    {
        $this->memo = new Memo();
        $this->memo->Name = 'TestMemo';
    }

    public function testTextProperty(): void
    {
        $this->memo->Text = "Line 1\nLine 2\nLine 3";
        $this->assertSame("Line 1\nLine 2\nLine 3", $this->memo->Text);
    }

    public function testLinesProperty(): void
    {
        $lines = ['Line 1', 'Line 2', 'Line 3'];
        $this->memo->Lines = $lines;
        $this->assertSame($lines, $this->memo->Lines);
    }

    public function testReadOnlyProperty(): void
    {
        $this->memo->ReadOnly = true;
        $this->assertTrue($this->memo->ReadOnly);
    }

    public function testMaxLengthProperty(): void
    {
        $this->memo->MaxLength = 1000;
        $this->assertSame(1000, $this->memo->MaxLength);
    }

    public function testPlaceholderProperty(): void
    {
        $this->memo->Placeholder = 'Enter text here...';
        $this->assertSame('Enter text here...', $this->memo->Placeholder);
    }

    public function testWordWrapProperty(): void
    {
        $this->memo->WordWrap = false;
        $this->assertFalse($this->memo->WordWrap);
    }

    public function testScrollBarsProperty(): void
    {
        $this->memo->ScrollBars = 'ssBoth';
        $this->assertSame('ssBoth', $this->memo->ScrollBars);
    }
}
