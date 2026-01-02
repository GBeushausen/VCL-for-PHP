<?php

declare(strict_types=1);

namespace VCL\Graphics;

use VCL\Core\Persistent;
use VCL\Graphics\Enums\PenStyle;

/**
 * Pen is used to draw lines or outline shapes on a canvas.
 *
 * Use Pen to describe the attributes of a pen when drawing something to a canvas.
 * Pen encapsulates the pen properties that are selected into the canvas.
 */
class Pen extends Persistent
{
    private string $_color = '#000000';
    private int $_width = 1;
    private PenStyle $_style = PenStyle::Solid;
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

    public int $Width {
        get => $this->_width;
        set {
            $this->_width = max(1, $value);
            $this->_modified = true;
        }
    }

    public PenStyle|string $Style {
        get => $this->_style;
        set {
            $this->_style = $value instanceof PenStyle ? $value : PenStyle::from($value);
            $this->_modified = true;
        }
    }

    /**
     * Get owner of this pen.
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
     * Assign pen properties to another pen.
     */
    public function assignTo(Persistent $dest): void
    {
        if (!($dest instanceof Pen)) {
            $dest->assignError($this);
        }

        $dest->Color = $this->_color;
        $dest->Width = $this->_width;
        $dest->Style = $this->_style;
    }

    // Legacy getters/setters
    public function getColor(): string { return $this->_color; }
    public function setColor(string $value): void { $this->Color = $value; }
    public function defaultColor(): string { return '#000000'; }

    public function getWidth(): int { return $this->_width; }
    public function setWidth(int $value): void { $this->Width = $value; }
    public function defaultWidth(): string { return '1'; }

    public function getStyle(): PenStyle|string { return $this->_style; }
    public function setStyle(PenStyle|string $value): void { $this->Style = $value; }
    public function defaultStyle(): string { return 'psSolid'; }
}
