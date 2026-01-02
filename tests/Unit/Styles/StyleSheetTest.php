<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\Styles;

use PHPUnit\Framework\TestCase;
use VCL\Styles\StyleSheet;
use VCL\Styles\CustomStyleSheet;

class StyleSheetTest extends TestCase
{
    private StyleSheet $stylesheet;

    protected function setUp(): void
    {
        $this->stylesheet = new StyleSheet();
        $this->stylesheet->Name = 'TestStyleSheet';
    }

    public function testDefaultFileName(): void
    {
        $this->assertSame('', $this->stylesheet->FileName);
    }

    public function testFileNameProperty(): void
    {
        // Use a non-existent file - should still set the property
        $this->stylesheet->FileName = 'styles.css';
        $this->assertSame('styles.css', $this->stylesheet->FileName);
    }

    public function testDefaultIncludeStandard(): void
    {
        $this->assertFalse($this->stylesheet->IncludeStandard);
    }

    public function testIncludeStandardProperty(): void
    {
        $this->stylesheet->IncludeStandard = true;
        $this->assertTrue($this->stylesheet->IncludeStandard);
    }

    public function testDefaultIncludeID(): void
    {
        $this->assertFalse($this->stylesheet->IncludeID);
    }

    public function testIncludeIDProperty(): void
    {
        $this->stylesheet->IncludeID = true;
        $this->assertTrue($this->stylesheet->IncludeID);
    }

    public function testDefaultIncludeSubStyle(): void
    {
        $this->assertFalse($this->stylesheet->IncludeSubStyle);
    }

    public function testIncludeSubStyleProperty(): void
    {
        $this->stylesheet->IncludeSubStyle = true;
        $this->assertTrue($this->stylesheet->IncludeSubStyle);
    }

    public function testStylesPropertyReturnsArray(): void
    {
        $this->assertIsArray($this->stylesheet->Styles);
    }

    public function testHasStyleReturnsFalseForNonExistent(): void
    {
        $this->assertFalse($this->stylesheet->hasStyle('.nonexistent'));
    }

    public function testExtendsCustomStyleSheet(): void
    {
        $this->assertInstanceOf(CustomStyleSheet::class, $this->stylesheet);
    }

    public function testIsComponent(): void
    {
        $this->assertInstanceOf(\VCL\Core\Component::class, $this->stylesheet);
    }
}
