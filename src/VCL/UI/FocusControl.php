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
     * Override this method in subclasses to customize child rendering.
     */
    protected function dumpChildren(): void
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
    // HTMX SUPPORT
    // =========================================================================

    /**
     * Check if the owner page has htmx enabled.
     */
    protected function isHtmxEnabled(): bool
    {
        $owner = $this->owner;
        while ($owner !== null) {
            if (method_exists($owner, 'getUseHtmx') && $owner->getUseHtmx()) {
                return true;
            }
            $owner = $owner->owner ?? null;
        }
        return false;
    }

    /**
     * Generate htmx attributes for an event.
     *
     * @param string $eventName The VCL event name (e.g., 'onclick', 'onchange')
     * @param string $trigger The htmx trigger (e.g., 'click', 'change', 'keyup')
     * @param string|null $target Optional target selector (defaults to control_result div)
     * @param string $swap The swap method (innerHTML, outerHTML, etc.)
     * @return string HTML attributes string
     */
    protected function getHtmxAttributes(
        string $eventName,
        string $trigger,
        ?string $target = null,
        string $swap = 'innerHTML'
    ): string {
        $ownerName = $this->owner !== null ? $this->owner->Name : '';
        $action = $_SERVER['PHP_SELF'] ?? '';

        // Default target is a result div next to this control
        if ($target === null) {
            $target = '#' . $this->Name . '_result';
        }

        $attrs = [];
        $attrs[] = sprintf('hx-post="%s"', htmlspecialchars($action));
        $attrs[] = sprintf('hx-trigger="%s"', htmlspecialchars($trigger));
        $attrs[] = sprintf('hx-target="%s"', htmlspecialchars($target));
        $attrs[] = sprintf('hx-swap="%s"', htmlspecialchars($swap));

        // Include all form values
        $attrs[] = sprintf('hx-include="#%s_form"', htmlspecialchars($ownerName));

        // Add VCL metadata (use JSON_HEX flags for safe HTML attribute escaping)
        $vclVals = json_encode([
            '_vcl_form' => $ownerName,
            '_vcl_control' => $this->Name,
            '_vcl_event' => $eventName,
        ], JSON_HEX_APOS | JSON_HEX_QUOT);
        $attrs[] = sprintf("hx-vals='%s'", $vclVals);

        return implode(' ', $attrs);
    }

    /**
     * Generate htmx attributes for OnClick event.
     */
    protected function getHtmxClickAttributes(?string $target = null): string
    {
        return $this->getHtmxAttributes('onclick', 'click', $target);
    }

    /**
     * Generate htmx attributes for OnChange event.
     */
    protected function getHtmxChangeAttributes(?string $target = null): string
    {
        return $this->getHtmxAttributes('onchange', 'change', $target);
    }

    /**
     * Generate htmx attributes for OnSubmit event.
     */
    protected function getHtmxSubmitAttributes(?string $target = null): string
    {
        return $this->getHtmxAttributes('onsubmit', 'submit', $target);
    }

    /**
     * Generate htmx attributes for keyboard events with debounce.
     *
     * @param string $eventName The VCL event name
     * @param int $delay Debounce delay in milliseconds
     */
    protected function getHtmxKeyAttributes(
        string $eventName = 'onkeyup',
        int $delay = 300,
        ?string $target = null
    ): string {
        $trigger = sprintf('keyup changed delay:%dms', $delay);
        return $this->getHtmxAttributes($eventName, $trigger, $target);
    }

    /**
     * Output htmx result div for this control.
     * Call this in dumpContents() after rendering the control.
     */
    protected function dumpHtmxResultDiv(): void
    {
        if ($this->isHtmxEnabled()) {
            echo sprintf('<div id="%s_result"></div>', htmlspecialchars($this->Name));
        }
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
