<?php

declare(strict_types=1);

namespace VCL\ExtCtrls;

use VCL\UI\GraphicControl;
use VCL\Graphics\Canvas;
use VCL\ExtCtrls\Enums\BevelShape;
use VCL\ExtCtrls\Enums\BevelStyle;

/**
 * Bevel represents a beveled outline.
 *
 * Use Bevel to create beveled boxes, frames, or lines.
 * The bevel can appear raised or lowered.
 *
 * PHP 8.4 version with Property Hooks.
 */
class Bevel extends GraphicControl
{
    protected BevelShape|string $_shape = BevelShape::Box;
    protected BevelStyle|string $_bevelstyle = BevelStyle::Lowered;
    public ?Canvas $_canvas = null;

    // Property Hooks
    public BevelShape|string $Shape {
        get => $this->_shape;
        set => $this->_shape = $value instanceof BevelShape ? $value : BevelShape::from($value);
    }

    public BevelStyle|string $BevelStyle {
        get => $this->_bevelstyle;
        set => $this->_bevelstyle = $value instanceof BevelStyle ? $value : BevelStyle::from($value);
    }

    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);
        $this->_canvas = new Canvas($this);
    }

    /**
     * Dump header code for canvas initialization.
     */
    public function dumpHeaderCode(): void
    {
        if ($this->_canvas !== null) {
            $this->_canvas->InitLibrary();
        }

        if (($this->ControlState & CS_DESIGNING) === CS_DESIGNING) {
            $name = htmlspecialchars($this->Name);
            echo "<div id=\"{$name}_outer\" style=\"Z-INDEX:2;WIDTH:{$this->_width}px;HEIGHT:{$this->_height}px\">";
        }
    }

    /**
     * Render the bevel.
     */
    protected function dumpContents(): void
    {
        if ($this->_canvas === null) {
            return;
        }

        $this->_canvas->BeginDraw();

        $w = max($this->_width, 1);
        $h = max($this->_height, 1);

        // Determine colors based on bevel style
        $style = $this->_bevelstyle instanceof BevelStyle
            ? $this->_bevelstyle
            : BevelStyle::from($this->_bevelstyle);

        if ($style === BevelStyle::Lowered) {
            $color1 = '#000000';
            $color2 = '#EEEEEE';
        } else {
            $color1 = '#EEEEEE';
            $color2 = '#000000';
        }

        // Draw shape based on type
        $shape = $this->_shape instanceof BevelShape
            ? $this->_shape
            : BevelShape::from($this->_shape);

        switch ($shape) {
            case BevelShape::Frame:
                $temp = $color1;
                $color1 = $color2;
                $this->_canvas->BevelRect(1, 1, $w - 1, $h - 1, $color1, $color2);
                $color2 = $temp;
                $color1 = $temp;
                $this->_canvas->BevelRect(0, 0, $w - 2, $h - 2, $color1, $color2);
                break;

            case BevelShape::TopLine:
                $this->_canvas->BevelLine($color1, 0, 0, $w, 0);
                $this->_canvas->BevelLine($color2, 0, 1, $w, 1);
                break;

            case BevelShape::BottomLine:
                $this->_canvas->BevelLine($color1, 0, $h - 2, $w, $h - 2);
                $this->_canvas->BevelLine($color2, 0, $h - 1, $w, $h - 1);
                break;

            case BevelShape::LeftLine:
                $this->_canvas->BevelLine($color1, 0, 0, 0, $h);
                $this->_canvas->BevelLine($color2, 1, 0, 1, $h);
                break;

            case BevelShape::RightLine:
                $this->_canvas->BevelLine($color1, $w - 2, 0, $w - 2, $h);
                $this->_canvas->BevelLine($color2, $w - 1, 0, $w - 1, $h);
                break;

            case BevelShape::Spacer:
                // Nothing to draw
                break;

            default: // Box
                $this->_canvas->BevelRect(0, 0, $w - 1, $h - 1, $color1, $color2);
                break;
        }

        $this->_canvas->EndDraw();
    }

    /**
     * Override render.
     */
    public function render(): string
    {
        ob_start();
        $this->dumpContents();
        return ob_get_clean();
    }

    // Legacy getters/setters
    public function getShape(): BevelShape|string { return $this->_shape; }
    public function setShape(BevelShape|string $value): void { $this->Shape = $value; }
    public function defaultShape(): string { return 'bsBox'; }

    public function getBevelStyle(): BevelStyle|string { return $this->_bevelstyle; }
    public function setBevelStyle(BevelStyle|string $value): void { $this->BevelStyle = $value; }
    public function defaultBevelStyle(): string { return 'bsLowered'; }
}
