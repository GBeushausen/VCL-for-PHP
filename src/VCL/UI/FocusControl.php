<?php

declare(strict_types=1);

namespace VCL\UI;

use VCL\Core\Collection;
use VCL\Graphics\Layout;

/**
 * FocusControl is the base class for controls that can receive focus.
 *
 * This class adds layout management and child control propagation methods.
 * Controls that can receive keyboard focus should inherit from this class.
 *
 * PHP 8.4 version with Property Hooks.
 */
class FocusControl extends Control
{
    protected ?Layout $_layout = null;

    // =========================================================================
    // PROPERTY HOOKS
    // =========================================================================

    public Layout $Layout {
        get {
            if ($this->_layout === null) {
                $this->_layout = new Layout();
                $this->_layout->_control = $this;
            }
            return $this->_layout;
        }
        set {
            if (is_object($value)) {
                $this->_layout = $value;
                $this->_layout->_control = $this;
            }
        }
    }

    public int $ControlCount {
        get => $this->controls?->count() ?? 0;
    }

    // =========================================================================
    // CONSTRUCTOR
    // =========================================================================

    public function __construct(?object $aowner = null)
    {
        // Create controls list first
        $this->controls = new Collection();

        // Call inherited constructor
        parent::__construct($aowner);

        // Create layout
        $this->_layout = new Layout();
        $this->_layout->_control = $this;
    }

    // =========================================================================
    // CHILDREN PROPERTY UPDATES
    // =========================================================================

    /**
     * Updates fonts for all children that have ParentFont=true.
     */
    public function updateChildrenFonts(): void
    {
        if ($this->controls === null) {
            return;
        }

        foreach ($this->controls->items as $child) {
            if ($child instanceof Control && $child->ParentFont) {
                $child->updateParentFont();
            }
        }
    }

    /**
     * Updates colors for all children that have ParentColor=true.
     */
    public function updateChildrenColors(): void
    {
        if ($this->controls === null) {
            return;
        }

        foreach ($this->controls->items as $child) {
            if ($child instanceof Control && $child->ParentColor) {
                $child->updateParentColor();
            }
        }
    }

    /**
     * Updates ShowHint for all children that have ParentShowHint=true.
     */
    public function updateChildrenShowHints(): void
    {
        if ($this->controls === null) {
            return;
        }

        foreach ($this->controls->items as $child) {
            if ($child instanceof Control && $child->ParentShowHint) {
                $child->updateParentShowHint();
            }
        }
    }

    /**
     * Updates all parent-dependent properties for children.
     */
    public function updateChildrenParentProperties(): void
    {
        if ($this->controls === null) {
            return;
        }

        foreach ($this->controls->items as $child) {
            if ($child instanceof Control) {
                if ($child->ParentColor || $child->ParentFont || $child->ParentShowHint) {
                    $child->updateParentProperties();
                }
            }
        }
    }

    // =========================================================================
    // CHILD CONTROL METHODS
    // =========================================================================

    /**
     * Dumps all children by calling show() on each control.
     */
    public function dumpChildren(): void
    {
        if ($this->controls === null) {
            return;
        }

        foreach ($this->controls->items as $child) {
            if ($child instanceof Control) {
                $child->show();
            }
        }
    }

    /**
     * Dumps children using the assigned layout.
     */
    public function dumpLayoutContents(array $exclude = []): void
    {
        $this->Layout->dumpLayoutContents($exclude);
    }

    // =========================================================================
    // JAVASCRIPT EVENTS
    // =========================================================================

    /**
     * Dump JavaScript events for this control.
     * Override in subclasses to add specific events.
     */
    public function dumpJsEvents(): void
    {
        // Base implementation - subclasses can override
    }

    // =========================================================================
    // LEGACY GETTERS/SETTERS
    // =========================================================================

    public function readLayout(): Layout
    {
        return $this->Layout;
    }

    public function writeLayout(Layout $value): void
    {
        $this->Layout = $value;
    }

    public function readControlCount(): int
    {
        return $this->ControlCount;
    }
}
