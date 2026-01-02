<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\Graphics;

use PHPUnit\Framework\TestCase;
use VCL\Graphics\Font;
use VCL\Graphics\Enums\TextAlign;
use VCL\Graphics\Enums\FontStyle;

class FontTest extends TestCase
{
    private Font $font;

    protected function setUp(): void
    {
        $this->font = new Font();
    }

    public function testDefaultFamily(): void
    {
        $this->assertSame('Verdana', $this->font->Family);
    }

    public function testFamilyProperty(): void
    {
        $this->font->Family = 'Arial';
        $this->assertSame('Arial', $this->font->Family);
    }

    public function testDefaultSize(): void
    {
        $this->assertSame('10px', $this->font->Size);
    }

    public function testSizeProperty(): void
    {
        $this->font->Size = '14px';
        $this->assertSame('14px', $this->font->Size);
    }

    public function testColorProperty(): void
    {
        $this->font->Color = '#FF0000';
        $this->assertSame('#FF0000', $this->font->Color);
    }

    public function testDefaultColorIsEmpty(): void
    {
        $this->assertSame('', $this->font->Color);
    }

    public function testWeightProperty(): void
    {
        $this->font->Weight = 'bold';
        $this->assertSame('bold', $this->font->Weight);
    }

    public function testAlignProperty(): void
    {
        $this->font->Align = TextAlign::Center;
        $this->assertEquals(TextAlign::Center, $this->font->Align);
    }

    public function testStyleProperty(): void
    {
        $this->font->Style = FontStyle::Italic;
        $this->assertEquals(FontStyle::Italic, $this->font->Style);
    }

    public function testLineHeightProperty(): void
    {
        $this->font->LineHeight = '1.5';
        $this->assertSame('1.5', $this->font->LineHeight);
    }

    public function testReadFontStringContainsFamily(): void
    {
        $fontString = $this->font->readFontString();
        $this->assertStringContainsString('font-family: Verdana', $fontString);
    }

    public function testReadFontStringContainsSize(): void
    {
        $fontString = $this->font->readFontString();
        $this->assertStringContainsString('font-size: 10px', $fontString);
    }

    public function testReadFontStringContainsColor(): void
    {
        $this->font->Color = '#0000FF';
        $fontString = $this->font->readFontString();
        $this->assertStringContainsString('color: #0000FF', $fontString);
    }

    public function testBatchUpdateMode(): void
    {
        $this->font->startUpdate();
        $this->assertTrue($this->font->isUpdating());
        $this->font->endUpdate();
        $this->assertFalse($this->font->isUpdating());
    }

    public function testAssignTo(): void
    {
        $this->font->Family = 'Helvetica';
        $this->font->Size = '16px';
        $this->font->Color = '#333333';

        $destFont = new Font();
        $this->font->assignTo($destFont);

        $this->assertSame('Helvetica', $destFont->Family);
        $this->assertSame('16px', $destFont->Size);
        $this->assertSame('#333333', $destFont->Color);
    }
}
