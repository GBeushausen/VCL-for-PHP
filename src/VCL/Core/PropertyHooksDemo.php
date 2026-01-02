<?php

declare(strict_types=1);

namespace VCL\Core;

/**
 * Demonstration of PHP 8.4 Property Hooks for VCL
 *
 * This shows how Property Hooks can replace the __get/__set magic methods
 * pattern currently used in VCL for property virtualization.
 *
 * Benefits:
 * - Native PHP syntax, no magic methods needed
 * - Better IDE support and autocompletion
 * - Type safety at language level
 * - Cleaner, more readable code
 * - Better performance (no method lookup overhead)
 */
class PropertyHooksDemo
{
    // Private backing fields
    private string $_caption = '';
    private int $_left = 0;
    private int $_top = 0;
    private int $_width = 100;
    private int $_height = 25;
    private bool $_visible = true;
    private bool $_enabled = true;

    /**
     * Caption property with get/set hooks
     * Demonstrates basic string property
     */
    public string $Caption {
        get => $this->_caption;
        set => $this->_caption = $value;
    }

    /**
     * Left position with validation
     * Demonstrates validation in setter
     */
    public int $Left {
        get => $this->_left;
        set {
            if ($value < 0) {
                $value = 0;
            }
            $this->_left = $value;
        }
    }

    /**
     * Top position with validation
     */
    public int $Top {
        get => $this->_top;
        set {
            if ($value < 0) {
                $value = 0;
            }
            $this->_top = $value;
        }
    }

    /**
     * Width with minimum value enforcement
     */
    public int $Width {
        get => $this->_width;
        set => $this->_width = max(1, $value);
    }

    /**
     * Height with minimum value enforcement
     */
    public int $Height {
        get => $this->_height;
        set => $this->_height = max(1, $value);
    }

    /**
     * Visible property - demonstrates boolean with side effects
     */
    public bool $Visible {
        get => $this->_visible;
        set {
            $this->_visible = $value;
            // Could trigger re-render here
        }
    }

    /**
     * Enabled property
     */
    public bool $Enabled {
        get => $this->_enabled;
        set => $this->_enabled = $value;
    }

    /**
     * Read-only computed property (virtual property)
     * Demonstrates computed/derived values
     */
    public string $BoundsRect {
        get => sprintf(
            'Rect(%d, %d, %d, %d)',
            $this->_left,
            $this->_top,
            $this->_left + $this->_width,
            $this->_top + $this->_height
        );
    }

    /**
     * ClientWidth - read-only, derived from Width
     */
    public int $ClientWidth {
        get => $this->_width - 2; // Subtract border
    }

    /**
     * ClientHeight - read-only, derived from Height
     */
    public int $ClientHeight {
        get => $this->_height - 2; // Subtract border
    }
}


/**
 * Example: How a Button control could look with Property Hooks
 */
class ButtonWithHooks
{
    private string $_caption = 'Button';
    private string $_name = '';
    private ?object $_parent = null;
    private bool $_default = false;
    private bool $_cancel = false;

    public string $Caption {
        get => $this->_caption;
        set => $this->_caption = $value;
    }

    public string $Name {
        get => $this->_name;
        set {
            // Validate name (alphanumeric only)
            if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $value)) {
                throw new \InvalidArgumentException("Invalid component name: {$value}");
            }
            $this->_name = $value;
        }
    }

    public ?object $Parent {
        get => $this->_parent;
        set {
            $this->_parent = $value;
            // Could notify parent of new child here
        }
    }

    /**
     * Default button (responds to Enter key)
     */
    public bool $Default {
        get => $this->_default;
        set {
            $this->_default = $value;
            // If setting as default, could unset other buttons
        }
    }

    /**
     * Cancel button (responds to Escape key)
     */
    public bool $Cancel {
        get => $this->_cancel;
        set => $this->_cancel = $value;
    }

    /**
     * Read-only: Check if button has a parent
     */
    public bool $HasParent {
        get => $this->_parent !== null;
    }
}
