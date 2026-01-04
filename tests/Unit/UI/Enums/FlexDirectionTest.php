<?php

declare(strict_types=1);

namespace VCL\Tests\Unit\UI\Enums;

use PHPUnit\Framework\TestCase;
use VCL\UI\Enums\FlexDirection;

class FlexDirectionTest extends TestCase
{
    public function testEnumValues(): void
    {
        $this->assertSame('row', FlexDirection::Row->value);
        $this->assertSame('row-reverse', FlexDirection::RowReverse->value);
        $this->assertSame('col', FlexDirection::Column->value);
        $this->assertSame('col-reverse', FlexDirection::ColumnReverse->value);
    }

    public function testToTailwind(): void
    {
        $this->assertSame('flex-row', FlexDirection::Row->toTailwind());
        $this->assertSame('flex-row-reverse', FlexDirection::RowReverse->toTailwind());
        $this->assertSame('flex-col', FlexDirection::Column->toTailwind());
        $this->assertSame('flex-col-reverse', FlexDirection::ColumnReverse->toTailwind());
    }

    public function testIsHorizontal(): void
    {
        $this->assertTrue(FlexDirection::Row->isHorizontal());
        $this->assertTrue(FlexDirection::RowReverse->isHorizontal());
        $this->assertFalse(FlexDirection::Column->isHorizontal());
        $this->assertFalse(FlexDirection::ColumnReverse->isHorizontal());
    }

    public function testIsVertical(): void
    {
        $this->assertFalse(FlexDirection::Row->isVertical());
        $this->assertFalse(FlexDirection::RowReverse->isVertical());
        $this->assertTrue(FlexDirection::Column->isVertical());
        $this->assertTrue(FlexDirection::ColumnReverse->isVertical());
    }
}
