<?php
/**
 * VCL for PHP 3.0
 *
 * Unit tests for Collection class
 */

declare(strict_types=1);

namespace VCL\Tests\Unit\Core;

use PHPUnit\Framework\TestCase;
use VCL\Core\Collection;

class CollectionTest extends TestCase
{
    private Collection $collection;

    protected function setUp(): void
    {
        $this->collection = new Collection();
    }

    public function testEmptyCollectionHasZeroCount(): void
    {
        $this->assertSame(0, $this->collection->count());
    }

    public function testAddItemIncreasesCount(): void
    {
        $this->collection->add('item1');
        $this->assertSame(1, $this->collection->count());

        $this->collection->add('item2');
        $this->assertSame(2, $this->collection->count());
    }

    public function testClearRemovesAllItems(): void
    {
        $this->collection->add('item1');
        $this->collection->add('item2');
        $this->collection->clear();

        $this->assertSame(0, $this->collection->count());
    }

    public function testIndexOfReturnsCorrectPosition(): void
    {
        $this->collection->add('first');
        $this->collection->add('second');
        $this->collection->add('third');

        $this->assertSame(1, $this->collection->indexOf('second'));
    }

    public function testIndexOfReturnsNegativeOneForMissingItem(): void
    {
        $this->collection->add('item');

        $this->assertSame(-1, $this->collection->indexOf('nonexistent'));
    }

    public function testItemsPropertyReturnsArray(): void
    {
        $this->collection->add('a');
        $this->collection->add('b');

        $items = $this->collection->items;
        $this->assertIsArray($items);
        $this->assertCount(2, $items);
    }
}
