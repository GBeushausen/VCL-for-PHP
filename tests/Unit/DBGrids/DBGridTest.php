<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\DBGrids;

use PHPUnit\Framework\TestCase;
use VCL\DBGrids\DBGrid;

class DBGridTest extends TestCase
{
    private DBGrid $grid;

    protected function setUp(): void
    {
        $this->grid = new DBGrid();
        $this->grid->Name = 'TestDBGrid';
    }

    public function testDefaultDimensions(): void
    {
        $this->assertSame(400, $this->grid->Width);
        $this->assertSame(200, $this->grid->Height);
    }

    public function testDefaultReadOnly(): void
    {
        $this->assertFalse($this->grid->ReadOnly);
    }

    public function testReadOnlyProperty(): void
    {
        $this->grid->ReadOnly = true;
        $this->assertTrue($this->grid->ReadOnly);
    }

    public function testDefaultFixedColumns(): void
    {
        $this->assertSame(0, $this->grid->FixedColumns);
    }

    public function testFixedColumnsProperty(): void
    {
        $this->grid->FixedColumns = 2;
        $this->assertSame(2, $this->grid->FixedColumns);
    }

    public function testFixedColumnsMinimumIsZero(): void
    {
        $this->grid->FixedColumns = -1;
        $this->assertSame(0, $this->grid->FixedColumns);
    }

    public function testDefaultShowHeader(): void
    {
        $this->assertTrue($this->grid->ShowHeader);
    }

    public function testShowHeaderProperty(): void
    {
        $this->grid->ShowHeader = false;
        $this->assertFalse($this->grid->ShowHeader);
    }

    public function testDefaultStriped(): void
    {
        $this->assertTrue($this->grid->Striped);
    }

    public function testStripedProperty(): void
    {
        $this->grid->Striped = false;
        $this->assertFalse($this->grid->Striped);
    }

    public function testDefaultHoverable(): void
    {
        $this->assertTrue($this->grid->Hoverable);
    }

    public function testHoverableProperty(): void
    {
        $this->grid->Hoverable = false;
        $this->assertFalse($this->grid->Hoverable);
    }

    public function testDefaultBordered(): void
    {
        $this->assertTrue($this->grid->Bordered);
    }

    public function testBorderedProperty(): void
    {
        $this->grid->Bordered = false;
        $this->assertFalse($this->grid->Bordered);
    }

    public function testDefaultSelectedRow(): void
    {
        $this->assertSame(-1, $this->grid->SelectedRow);
    }

    public function testSelectedRowProperty(): void
    {
        $this->grid->SelectedRow = 5;
        $this->assertSame(5, $this->grid->SelectedRow);
    }

    public function testHeaderClassProperty(): void
    {
        $this->grid->HeaderClass = 'custom-header';
        $this->assertSame('custom-header', $this->grid->HeaderClass);
    }

    public function testRowClassProperty(): void
    {
        $this->grid->RowClass = 'custom-row';
        $this->assertSame('custom-row', $this->grid->RowClass);
    }

    public function testAlternateRowClassProperty(): void
    {
        $this->grid->AlternateRowClass = 'alt-row';
        $this->assertSame('alt-row', $this->grid->AlternateRowClass);
    }

    public function testSelectedRowClassProperty(): void
    {
        $this->grid->SelectedRowClass = 'selected-row';
        $this->assertSame('selected-row', $this->grid->SelectedRowClass);
    }

    public function testJsOnDataChangedEvent(): void
    {
        $this->grid->jsOnDataChanged = 'handleDataChanged';
        $this->assertSame('handleDataChanged', $this->grid->jsOnDataChanged);
    }

    public function testJsOnRowChangedEvent(): void
    {
        $this->grid->jsOnRowChanged = 'handleRowChanged';
        $this->assertSame('handleRowChanged', $this->grid->jsOnRowChanged);
    }

    public function testOnClickEvent(): void
    {
        $this->grid->OnClick = 'handleClick';
        $this->assertSame('handleClick', $this->grid->OnClick);
    }

    public function testOnDblClickEvent(): void
    {
        $this->grid->OnDblClick = 'handleDblClick';
        $this->assertSame('handleDblClick', $this->grid->OnDblClick);
    }

    public function testGetSelectedRowDataReturnsNullWhenNoSelection(): void
    {
        $this->assertNull($this->grid->getSelectedRowData());
    }

    public function testIsCustomControl(): void
    {
        $this->assertInstanceOf(\VCL\UI\CustomControl::class, $this->grid);
    }
}
