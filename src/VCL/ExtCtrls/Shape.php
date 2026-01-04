<?php

declare(strict_types=1);

namespace VCL\ExtCtrls;

use VCL\UI\Control;
use VCL\Graphics\Pen;
use VCL\Graphics\Brush;
use VCL\Graphics\Canvas;
use VCL\ExtCtrls\Enums\ShapeType;

/**
 * Shape draws simple geometric shapes on a form.
 *
 * Use Shape to draw a rectangle, square, rounded rectangle, rounded square,
 * ellipse, or circle on a form. Use the Shape property to specify the shape,
 * Pen to specify the outline, and Brush to specify the fill pattern.
 *
 * PHP 8.4 version with Property Hooks.
 */
class Shape extends Control
{
    protected ShapeType|string $_shape = ShapeType::Rectangle;
    protected ?Pen $_pen = null;
    protected ?Brush $_brush = null;
    protected ?Canvas $_canvas = null;

    // Property Hooks
    public ShapeType|string $Shape {
        get => $this->_shape;
        set => $this->_shape = $value instanceof ShapeType ? $value : ShapeType::from($value);
    }

    public Pen $Pen {
        get {
            if ($this->_pen === null) {
                $this->_pen = new Pen();
                $this->_pen->_control = $this;
            }
            return $this->_pen;
        }
        set {
            if (is_object($value)) {
                $this->_pen = $value;
                $this->_pen->_control = $this;
            }
        }
    }

    public Brush $Brush {
        get {
            if ($this->_brush === null) {
                $this->_brush = new Brush();
                $this->_brush->_control = $this;
            }
            return $this->_brush;
        }
        set {
            if (is_object($value)) {
                $this->_brush = $value;
                $this->_brush->_control = $this;
            }
        }
    }

    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);
        $this->_width = 65;
        $this->_height = 65;
        $this->_pen = new Pen();
        $this->_pen->_control = $this;
        $this->_brush = new Brush();
        $this->_brush->_control = $this;
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
     * Render the shape.
     */
    protected function dumpContents(): void
    {
        if ($this->_canvas === null) {
            return;
        }

        $this->_canvas->BeginDraw();

        $penwidth = max($this->Pen->Width, 1);
        $shape = $this->_shape instanceof ShapeType ? $this->_shape : ShapeType::from($this->_shape);

        // Calculate coordinates based on shape type
        switch ($shape) {
            case ShapeType::Circle:
            case ShapeType::Square:
            case ShapeType::RoundSquare:
                $size = min($this->_width, $this->_height) / 2 - $penwidth * 4;
                $xc = $this->_width / 2;
                $yc = $this->_height / 2;
                $x1 = $xc - $size;
                $y1 = $yc - $size;
                $x2 = $xc + $size;
                $y2 = $yc + $size;
                break;

            default:
                $x1 = $penwidth;
                $y1 = $penwidth;
                $x2 = max($this->_width, 2) - $penwidth * 2;
                $y2 = max($this->_height, 2) - $penwidth * 2;
                $size = max($x2, $y2);
                break;
        }

        $w = max($this->_width, 1);
        $h = max($this->_height, 1);

        // Set canvas pen and brush
        $this->_canvas->Pen->Color = $this->Pen->Color;
        $this->_canvas->Pen->Width = $this->Pen->Width;
        $this->_canvas->Brush->Color = $this->Brush->Color;

        // Draw shape
        switch ($shape) {
            case ShapeType::Rectangle:
            case ShapeType::Square:
                $this->_canvas->FillRect($x1, $y1, $x2, $y2);
                $this->_canvas->Rectangle($x1, $y1, $x2, $y2);
                break;

            case ShapeType::RoundRect:
            case ShapeType::RoundSquare:
                $s = min($w, $h);
                $this->_canvas->RoundRect($x1, $y1, $x2, $y2, $s / 4, $s / 4);
                break;

            case ShapeType::Circle:
                $this->_canvas->Ellipse($x1, $y1, $x2 - 1, $y2 - 1);
                break;

            case ShapeType::Ellipse:
                $this->_canvas->Ellipse($x1, $y1, $x2, $y2);
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
    public function getShape(): ShapeType|string { return $this->_shape; }
    public function setShape(ShapeType|string $value): void { $this->Shape = $value; }
    public function defaultShape(): string { return 'stRectangle'; }

    public function getPen(): Pen { return $this->Pen; }
    public function setPen(Pen $value): void { $this->Pen = $value; }

    public function getBrush(): Brush { return $this->Brush; }
    public function setBrush(Brush $value): void { $this->Brush = $value; }
}
