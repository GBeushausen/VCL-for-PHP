<?php

declare(strict_types=1);

namespace VCL\UI;

/**
 * CustomListControl is the abstract base class for controls that display a list of items.
 *
 * It provides an abstract interface for list-based controls such as ListBox, ComboBox, etc.
 *
 * PHP 8.4 version.
 */
abstract class CustomListControl extends FocusControl
{
    protected int $_itemindex = -1;

    /**
     * Returns the number of items in the list.
     */
    abstract public function readCount(): int;

    /**
     * Returns the ItemIndex property value.
     */
    abstract public function readItemIndex(): int;

    /**
     * Sets the ItemIndex value.
     */
    abstract public function writeItemIndex(int $value): void;

    /**
     * Returns the default ItemIndex.
     */
    abstract public function defaultItemIndex(): int;

    /**
     * Adds an item to the list control.
     *
     * @param mixed $item Value of item to add
     * @param object|null $object Object to assign to the item
     * @param mixed $itemkey Key of the item in the array
     * @return int Number of items in the list after adding
     */
    abstract public function addItem(mixed $item, ?object $object = null, mixed $itemkey = null): int;

    /**
     * Deletes all items from the list control.
     */
    abstract public function clear(): void;

    /**
     * Removes the selection, leaving all items unselected.
     */
    abstract public function clearSelection(): void;

    /**
     * Selects all items or all text in the selected item.
     */
    abstract public function selectAll(): void;
}
