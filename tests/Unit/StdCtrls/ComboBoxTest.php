<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\StdCtrls;

use PHPUnit\Framework\TestCase;
use VCL\StdCtrls\ComboBox;

class ComboBoxTest extends TestCase
{
    private ComboBox $combo;

    protected function setUp(): void
    {
        $this->combo = new ComboBox();
        $this->combo->Name = 'TestComboBox';
    }

    public function testItemsProperty(): void
    {
        $items = ['Apple', 'Banana', 'Cherry'];
        $this->combo->Items = $items;
        $this->assertSame($items, $this->combo->Items);
    }

    public function testItemIndexProperty(): void
    {
        $this->combo->Items = ['A', 'B', 'C'];
        $this->combo->ItemIndex = 1;
        $this->assertSame(1, $this->combo->ItemIndex);
    }

    public function testTextProperty(): void
    {
        $this->combo->Text = 'Selected Value';
        $this->assertSame('Selected Value', $this->combo->Text);
    }

    public function testAddItem(): void
    {
        $this->combo->addItem('New Item');
        $items = $this->combo->Items;
        $this->assertContains('New Item', $items);
    }

    public function testClear(): void
    {
        $this->combo->Items = ['A', 'B', 'C'];
        $this->combo->clear();
        $this->assertEmpty($this->combo->Items);
    }
}
