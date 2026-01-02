<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\DBCtrls;

use PHPUnit\Framework\TestCase;
use VCL\DBCtrls\DBPaginator;

class DBPaginatorTest extends TestCase
{
    private DBPaginator $paginator;

    protected function setUp(): void
    {
        $this->paginator = new DBPaginator();
        $this->paginator->Name = 'TestPaginator';
    }

    public function testDefaultDimensions(): void
    {
        $this->assertSame(300, $this->paginator->Width);
        $this->assertSame(30, $this->paginator->Height);
    }

    public function testDefaultCaptions(): void
    {
        $this->assertSame('First', $this->paginator->CaptionFirst);
        $this->assertSame('Prev', $this->paginator->CaptionPrevious);
        $this->assertSame('Next', $this->paginator->CaptionNext);
        $this->assertSame('Last', $this->paginator->CaptionLast);
    }

    public function testCaptionFirstProperty(): void
    {
        $this->paginator->CaptionFirst = '<<';
        $this->assertSame('<<', $this->paginator->CaptionFirst);
    }

    public function testCaptionPreviousProperty(): void
    {
        $this->paginator->CaptionPrevious = '<';
        $this->assertSame('<', $this->paginator->CaptionPrevious);
    }

    public function testCaptionNextProperty(): void
    {
        $this->paginator->CaptionNext = '>';
        $this->assertSame('>', $this->paginator->CaptionNext);
    }

    public function testCaptionLastProperty(): void
    {
        $this->paginator->CaptionLast = '>>';
        $this->assertSame('>>', $this->paginator->CaptionLast);
    }

    public function testDefaultOrientation(): void
    {
        $this->assertSame('noHorizontal', $this->paginator->Orientation);
    }

    public function testOrientationProperty(): void
    {
        $this->paginator->Orientation = 'noVertical';
        $this->assertSame('noVertical', $this->paginator->Orientation);
    }

    public function testDefaultPageNumberFormat(): void
    {
        $this->assertSame('%d', $this->paginator->PageNumberFormat);
    }

    public function testPageNumberFormatProperty(): void
    {
        $this->paginator->PageNumberFormat = 'Page %d';
        $this->assertSame('Page %d', $this->paginator->PageNumberFormat);
    }

    public function testDefaultShowFirst(): void
    {
        $this->assertTrue($this->paginator->ShowFirst);
    }

    public function testShowFirstProperty(): void
    {
        $this->paginator->ShowFirst = false;
        $this->assertFalse($this->paginator->ShowFirst);
    }

    public function testDefaultShowLast(): void
    {
        $this->assertTrue($this->paginator->ShowLast);
    }

    public function testShowLastProperty(): void
    {
        $this->paginator->ShowLast = false;
        $this->assertFalse($this->paginator->ShowLast);
    }

    public function testDefaultShowNext(): void
    {
        $this->assertTrue($this->paginator->ShowNext);
    }

    public function testShowNextProperty(): void
    {
        $this->paginator->ShowNext = false;
        $this->assertFalse($this->paginator->ShowNext);
    }

    public function testDefaultShowPrevious(): void
    {
        $this->assertTrue($this->paginator->ShowPrevious);
    }

    public function testShowPreviousProperty(): void
    {
        $this->paginator->ShowPrevious = false;
        $this->assertFalse($this->paginator->ShowPrevious);
    }

    public function testDefaultShownRecordsCount(): void
    {
        $this->assertSame(10, $this->paginator->ShownRecordsCount);
    }

    public function testShownRecordsCountProperty(): void
    {
        $this->paginator->ShownRecordsCount = 20;
        $this->assertSame(20, $this->paginator->ShownRecordsCount);
    }

    public function testShownRecordsCountMinimumIsOne(): void
    {
        $this->paginator->ShownRecordsCount = 0;
        $this->assertSame(1, $this->paginator->ShownRecordsCount);
    }

    public function testOnClickEvent(): void
    {
        $this->paginator->OnClick = 'handleClick';
        $this->assertSame('handleClick', $this->paginator->OnClick);
    }

    public function testIsCustomControl(): void
    {
        $this->assertInstanceOf(\VCL\UI\CustomControl::class, $this->paginator);
    }
}
