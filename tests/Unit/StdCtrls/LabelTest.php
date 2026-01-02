<?php
/**
 * VCL for PHP 3.0
 *
 * Unit tests for Label class
 */

declare(strict_types=1);

namespace VCL\Tests\Unit\StdCtrls;

use PHPUnit\Framework\TestCase;
use VCL\StdCtrls\Label;

class LabelTest extends TestCase
{
    private Label $label;

    protected function setUp(): void
    {
        $this->label = new Label();
        $this->label->Name = 'TestLabel';
    }

    public function testCaptionProperty(): void
    {
        $this->label->Caption = 'Hello World';
        $this->assertSame('Hello World', $this->label->Caption);
    }

    public function testWordWrapProperty(): void
    {
        $this->label->WordWrap = true;
        $this->assertTrue($this->label->WordWrap);

        $this->label->WordWrap = false;
        $this->assertFalse($this->label->WordWrap);
    }

    public function testLinkProperty(): void
    {
        $this->label->Link = 'https://example.com';
        $this->assertSame('https://example.com', $this->label->Link);
    }

    public function testLinkTargetProperty(): void
    {
        $this->label->LinkTarget = '_blank';
        $this->assertSame('_blank', $this->label->LinkTarget);
    }
}
