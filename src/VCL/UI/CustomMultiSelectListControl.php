<?php

declare(strict_types=1);

namespace VCL\UI;

/**
 * CustomMultiSelectListControl is the abstract base class for list controls with multi-selection.
 *
 * Extends CustomListControl to add support for selecting multiple items at once.
 *
 * PHP 8.4 version.
 */
abstract class CustomMultiSelectListControl extends CustomListControl
{
    protected bool $_multiselect = false;

    /**
     * Returns the number of selected items in the list.
     */
    abstract public function readSelCount(): int;

    /**
     * Returns whether multi-selection is enabled.
     */
    abstract public function readMultiSelect(): bool;

    /**
     * Sets the multi-selection mode.
     */
    abstract public function writeMultiSelect(bool $value): void;

    /**
     * Returns the default multi-selection value.
     */
    abstract public function defaultMultiSelect(): bool;
}
