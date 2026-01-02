<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\StdCtrls;

use PHPUnit\Framework\TestCase;
use VCL\StdCtrls\CheckListBox;

class CheckListBoxTest extends TestCase
{
    private CheckListBox $listbox;

    protected function setUp(): void
    {
        $this->listbox = new CheckListBox();
        $this->listbox->Name = 'TestCheckListBox';
    }

    public function testItemsProperty(): void
    {
        $items = ['Item 1', 'Item 2', 'Item 3'];
        $this->listbox->Items = $items;
        $this->assertSame($items, $this->listbox->Items);
    }

    public function testAddItemReturnsCount(): void
    {
        $count = $this->listbox->addItem('New Item');
        $this->assertSame(1, $count);
        $this->assertContains('New Item', $this->listbox->Items);
    }

    public function testClear(): void
    {
        $this->listbox->Items = ['A', 'B', 'C'];
        $this->listbox->clear();
        $this->assertEmpty($this->listbox->Items);
    }

    public function testSelectAll(): void
    {
        $this->listbox->Items = ['A', 'B', 'C'];
        $this->listbox->selectAll();
        $checked = $this->listbox->Checked;
        $this->assertCount(3, $checked);
    }

    public function testDeselectAll(): void
    {
        $this->listbox->Items = ['A', 'B', 'C'];
        $this->listbox->selectAll();
        $this->listbox->deselectAll();
        $this->assertEmpty($this->listbox->Checked);
    }
}
