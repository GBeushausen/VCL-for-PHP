<?php

declare(strict_types=1);

namespace VCL\Graphics;

use VCL\Core\Persistent;

/**
 * Brush represents the color and pattern used to fill solid shapes.
 *
 * Brush encapsulates several properties to hold all the attributes to fill solid shapes,
 * such as rectangles and ellipses, with a color or pattern.
 */
class Brush extends Persistent
{
    private string $_color = '#FFFFFF';
    private bool $_modified = false;

    public ?object $_control = null;

    // Property Hooks
    public string $Color {
        get => $this->_color;
        set {
            $this->_color = $value;
            $this->_modified = true;
        }
    }

    /**
     * Get owner of this brush.
     */
    public function readOwner(): mixed
    {
        return $this->_control;
    }

    /**
     * Mark as modified.
     */
    public function modified(): void
    {
        $this->_modified = true;
    }

    /**
     * Check if modified.
     */
    public function isModified(): bool
    {
        return $this->_modified;
    }

    /**
     * Reset modified flag.
     */
    public function resetModified(): void
    {
        $this->_modified = false;
    }

    /**
     * Assign brush properties to another brush.
     */
    public function assignTo(Persistent $dest): void
    {
        if (!($dest instanceof Brush)) {
            $dest->assignError($this);
        }

        $dest->Color = $this->_color;
    }

    // Legacy getters/setters
    public function getColor(): string { return $this->_color; }
    public function setColor(string $value): void { $this->Color = $value; }
    public function defaultColor(): string { return ''; }
}
