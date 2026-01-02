<?php

declare(strict_types=1);

namespace VCL\Core;

use VCL\Core\Exception\CollectionException;

/**
 * A class for storing and managing a list of objects.
 *
 * Collection, which stores an array of items, is often used to maintain lists
 * of objects. It provides methods to:
 * - Add or delete objects in the list
 * - Rearrange objects in the list
 * - Locate and access objects in the list
 * - Sort objects in the list
 *
 * PHP 8.4 version with modern features:
 * - Implements Countable and IteratorAggregate for foreach support
 * - Uses typed properties
 * - Provides both method and array-style access
 *
 * @implements \IteratorAggregate<int, mixed>
 */
class Collection extends VCLObject implements \Countable, \IteratorAggregate
{
    /**
     * Items array - public for legacy compatibility
     * @var array<int, mixed>
     */
    public array $items = [];

    /**
     * Number of items in the collection (read-only)
     */
    public int $Count {
        get => count($this->items);
    }

    public function __construct()
    {
        parent::__construct();
        $this->clear();
    }

    /**
     * Inserts a new item at the end of the list
     *
     * @param mixed $item Object to add to the list
     * @return int Number of items in the collection
     */
    public function add(mixed $item): int
    {
        end($this->items);
        $this->items[] = $item;
        return $this->count();
    }

    /**
     * Deletes all items from the list
     */
    public function clear(): void
    {
        $this->items = [];
    }

    /**
     * Removes the item at the specified index
     *
     * @param int $index Index of the item to delete
     * @throws CollectionException If index is out of bounds
     */
    public function delete(int $index): void
    {
        if ($index >= $this->count()) {
            throw new CollectionException($index);
        }
        array_splice($this->items, $index, 1);
    }

    /**
     * Returns the index of the first entry with a specified value
     *
     * @param mixed $item Item to find
     * @return int Index of the item or -1 if not found
     */
    public function indexOf(mixed $item): int
    {
        foreach ($this->items as $k => $v) {
            if ($v === $item) {
                return $k;
            }
        }
        return -1;
    }


    /**
     * Removes the first reference to the specified item
     *
     * @param mixed $item Item to remove
     * @return int Index of the removed item or -1 if not found
     */
    public function remove(mixed $item): int
    {
        $index = $this->indexOf($item);
        if ($index >= 0) {
            $this->delete($index);
        }
        return $index;
    }

    /**
     * Returns the number of items in the collection
     * Implements Countable interface
     */
    public function count(): int
    {
        return count($this->items);
    }

    /**
     * Returns the last element from the collection
     *
     * @return mixed Last item or null if empty
     */
    public function last(): mixed
    {
        if ($this->count() >= 1) {
            return $this->items[count($this->items) - 1];
        }
        return null;
    }

    /**
     * Returns the first element from the collection
     *
     * @return mixed First item or null if empty
     */
    public function first(): mixed
    {
        return $this->items[0] ?? null;
    }

    /**
     * Get item at specific index
     *
     * @param int $index Index of item
     * @return mixed Item at index or null if not found
     */
    public function get(int $index): mixed
    {
        return $this->items[$index] ?? null;
    }

    /**
     * Check if collection contains an item
     *
     * @param mixed $item Item to check
     * @return bool True if item exists
     */
    public function contains(mixed $item): bool
    {
        return $this->indexOf($item) >= 0;
    }

    /**
     * Check if collection is empty
     */
    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    /**
     * Convert collection to array
     *
     * @return array<int, mixed>
     */
    public function toArray(): array
    {
        return $this->items;
    }

    /**
     * IteratorAggregate implementation for foreach support
     *
     * @return \Traversable<int, mixed>
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->items);
    }

    /**
     * Insert item at specific position
     *
     * @param int $index Position to insert at
     * @param mixed $item Item to insert
     */
    public function insert(int $index, mixed $item): void
    {
        array_splice($this->items, $index, 0, [$item]);
    }

    /**
     * Exchange positions of two items
     *
     * @param int $index1 First index
     * @param int $index2 Second index
     */
    public function exchange(int $index1, int $index2): void
    {
        if ($index1 < $this->count() && $index2 < $this->count()) {
            $temp = $this->items[$index1];
            $this->items[$index1] = $this->items[$index2];
            $this->items[$index2] = $temp;
        }
    }

    /**
     * Move item from one position to another
     *
     * @param int $currentIndex Current position
     * @param int $newIndex New position
     */
    public function move(int $currentIndex, int $newIndex): void
    {
        if ($currentIndex < $this->count() && $newIndex < $this->count()) {
            $item = $this->items[$currentIndex];
            $this->delete($currentIndex);
            $this->insert($newIndex, $item);
        }
    }
}
