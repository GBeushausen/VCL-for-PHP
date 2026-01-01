<?php

declare(strict_types=1);

namespace VCL\UI;

use VCL\Core\Component;
use VCL\UI\Enums\Alignment;
use VCL\UI\Enums\Anchors;
use VCL\UI\Enums\Cursor;

/**
 * Control is the base class for all visual components.
 *
 * Controls are components that are visible at runtime. They have properties
 * for position, size, appearance, and user interaction through events.
 *
 * This class uses PHP 8.4 Property Hooks for clean getter/setter syntax.
 */
class Control extends Component
{
    // Position and size backing fields
    private int $_left = 0;
    private int $_top = 0;
    private ?int $_width = null;
    private ?int $_height = null;

    // Appearance backing fields
    private string $_caption = '';
    private string $_color = '';
    private string $_hint = '';
    private bool $_visible = true;
    private bool $_enabled = true;
    private bool $_showHint = false;

    // Parent/alignment backing fields
    private ?Control $_parent = null;
    private Alignment $_align = Alignment::None;
    private Anchors $_alignment = Anchors::None;
    private Cursor $_cursor = Cursor::Default;

    // Font (lazy loaded)
    private ?object $_font = null;
    private bool $_parentFont = true;
    private bool $_parentColor = true;
    private bool $_parentShowHint = true;

    // =========================================================================
    // POSITION PROPERTIES
    // =========================================================================

    /**
     * Left position in pixels
     */
    public int $Left {
        get => $this->_left;
        set => $this->_left = $value;
    }

    /**
     * Top position in pixels
     */
    public int $Top {
        get => $this->_top;
        set => $this->_top = $value;
    }

    /**
     * Width in pixels (null = auto)
     */
    public ?int $Width {
        get => $this->_width;
        set {
            if ($value !== null && $value < 0) {
                $value = 0;
            }
            $this->_width = $value;
        }
    }

    /**
     * Height in pixels (null = auto)
     */
    public ?int $Height {
        get => $this->_height;
        set {
            if ($value !== null && $value < 0) {
                $value = 0;
            }
            $this->_height = $value;
        }
    }

    /**
     * Computed: Right edge position
     */
    public int $Right {
        get => $this->_left + ($this->_width ?? 0);
    }

    /**
     * Computed: Bottom edge position
     */
    public int $Bottom {
        get => $this->_top + ($this->_height ?? 0);
    }

    /**
     * Computed: Client width (inside borders/padding)
     */
    public int $ClientWidth {
        get => ($this->_width ?? 0) - $this->getBorderWidth() * 2;
    }

    /**
     * Computed: Client height (inside borders/padding)
     */
    public int $ClientHeight {
        get => ($this->_height ?? 0) - $this->getBorderWidth() * 2;
    }

    // =========================================================================
    // APPEARANCE PROPERTIES
    // =========================================================================

    /**
     * Caption/text displayed on the control
     */
    public string $Caption {
        get => $this->_caption;
        set => $this->_caption = $value;
    }

    /**
     * Background color (CSS color value)
     */
    public string $Color {
        get {
            if ($this->_parentColor && $this->_parent !== null) {
                return $this->_parent->Color;
            }
            return $this->_color;
        }
        set {
            $this->_color = $value;
            $this->_parentColor = false;
        }
    }

    /**
     * Hint text shown on hover
     */
    public string $Hint {
        get => $this->_hint;
        set => $this->_hint = $value;
    }

    /**
     * Whether the control is visible
     */
    public bool $Visible {
        get => $this->_visible;
        set => $this->_visible = $value;
    }

    /**
     * Whether the control can receive input
     */
    public bool $Enabled {
        get => $this->_enabled;
        set => $this->_enabled = $value;
    }

    /**
     * Whether to show the hint
     */
    public bool $ShowHint {
        get {
            if ($this->_parentShowHint && $this->_parent !== null) {
                return $this->_parent->ShowHint;
            }
            return $this->_showHint;
        }
        set {
            $this->_showHint = $value;
            $this->_parentShowHint = false;
        }
    }

    /**
     * Mouse cursor when hovering
     */
    public Cursor $Cursor {
        get => $this->_cursor;
        set => $this->_cursor = $value;
    }

    // =========================================================================
    // LAYOUT PROPERTIES
    // =========================================================================

    /**
     * Parent control (visual container)
     */
    public ?Control $Parent {
        get => $this->_parent;
        set {
            if ($this->_parent === $value) {
                return;
            }

            // Remove from old parent
            if ($this->_parent !== null) {
                $this->_parent->removeChild($this);
            }

            $this->_parent = $value;

            // Add to new parent
            if ($value !== null) {
                $value->addChild($this);
            }
        }
    }

    /**
     * Alignment within parent
     */
    public Alignment $Align {
        get => $this->_align;
        set => $this->_align = $value;
    }

    /**
     * Content alignment (text alignment)
     */
    public Anchors $Alignment {
        get => $this->_alignment;
        set => $this->_alignment = $value;
    }

    /**
     * Use parent's font settings
     */
    public bool $ParentFont {
        get => $this->_parentFont;
        set => $this->_parentFont = $value;
    }

    /**
     * Use parent's color
     */
    public bool $ParentColor {
        get => $this->_parentColor;
        set => $this->_parentColor = $value;
    }

    // =========================================================================
    // CHILD CONTROLS
    // =========================================================================

    /** @var array<Control> Visual children */
    protected array $_children = [];

    /**
     * Number of child controls
     */
    public int $ControlCount {
        get => count($this->_children);
    }

    /**
     * Add a child control
     */
    public function addChild(Control $child): void
    {
        if (!in_array($child, $this->_children, true)) {
            $this->_children[] = $child;
        }
    }

    /**
     * Remove a child control
     */
    public function removeChild(Control $child): void
    {
        $key = array_search($child, $this->_children, true);
        if ($key !== false) {
            unset($this->_children[$key]);
            $this->_children = array_values($this->_children);
        }
    }

    /**
     * Get child control by index
     */
    public function getControl(int $index): ?Control
    {
        return $this->_children[$index] ?? null;
    }

    /**
     * Iterate over child controls
     *
     * @return \Generator<Control>
     */
    public function getControls(): \Generator
    {
        foreach ($this->_children as $child) {
            yield $child;
        }
    }

    // =========================================================================
    // RENDERING
    // =========================================================================

    /**
     * Get the border width for this control
     */
    protected function getBorderWidth(): int
    {
        return 0;
    }

    /**
     * Generate inline CSS style
     */
    public function getStyle(): string
    {
        $styles = [];

        if ($this->_align !== Alignment::None) {
            $styles[] = 'position: ' . $this->_align->toCss();
        } else {
            $styles[] = "position: absolute";
            $styles[] = "left: {$this->_left}px";
            $styles[] = "top: {$this->_top}px";
        }

        if ($this->_width !== null) {
            $styles[] = "width: {$this->_width}px";
        }
        if ($this->_height !== null) {
            $styles[] = "height: {$this->_height}px";
        }

        if ($this->_color !== '') {
            $styles[] = "background-color: {$this->_color}";
        }

        if ($this->_cursor !== Cursor::Default) {
            $styles[] = "cursor: {$this->_cursor->toCss()}";
        }

        if (!$this->_visible) {
            $styles[] = "display: none";
        }

        return implode('; ', $styles);
    }

    /**
     * Generate HTML attributes
     */
    public function getAttributes(): string
    {
        $attrs = [];

        if ($this->Name !== '') {
            $attrs[] = 'id="' . htmlspecialchars($this->Name) . '"';
        }

        $style = $this->getStyle();
        if ($style !== '') {
            $attrs[] = 'style="' . htmlspecialchars($style) . '"';
        }

        if (!$this->_enabled) {
            $attrs[] = 'disabled';
        }

        if ($this->_hint !== '' && $this->ShowHint) {
            $attrs[] = 'title="' . htmlspecialchars($this->_hint) . '"';
        }

        return implode(' ', $attrs);
    }

    /**
     * Render the control to HTML
     */
    public function render(): string
    {
        return sprintf(
            '<div %s>%s</div>',
            $this->getAttributes(),
            htmlspecialchars($this->_caption)
        );
    }

    /**
     * Set bounds (position and size) in one call
     */
    public function setBounds(int $left, int $top, int $width, int $height): void
    {
        $this->_left = $left;
        $this->_top = $top;
        $this->_width = $width;
        $this->_height = $height;
    }
}
