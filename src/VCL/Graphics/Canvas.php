<?php

declare(strict_types=1);

namespace VCL\Graphics;

use VCL\Core\Persistent;

/**
 * Canvas provides an abstract drawing space for objects that must render their own images.
 *
 * Use Canvas as a drawing surface for objects that draw an image of themselves.
 * Canvas provides properties, events and methods that assist in creating an image by:
 * - Specifying the type of brush, pen and font to use
 * - Drawing and filling a variety of shapes and lines
 * - Writing text
 * - Rendering graphic images
 */
class Canvas extends Persistent
{
    protected ?Pen $_pen = null;
    protected ?Brush $_brush = null;
    protected ?Font $_font = null;
    protected string $_canvas = '';
    protected string $_object = '';
    protected ?object $_owner = null;

    public function __construct(?object $owner = null)
    {
        parent::__construct();

        $this->_pen = new Pen();
        $this->_pen->Width = 1;
        $this->_brush = new Brush();
        $this->_font = new Font();
        $this->_owner = $owner;
    }

    // Property Hooks
    public Pen $Pen {
        get => $this->_pen;
        set => $this->_pen = $value;
    }

    public Brush $Brush {
        get => $this->_brush;
        set => $this->_brush = $value;
    }

    public Font $Font {
        get => $this->_font;
        set => $this->_font = $value;
    }

    /**
     * Force brush color if modified.
     */
    protected function forceBrush(): void
    {
        if ($this->_brush->isModified()) {
            echo "{$this->_canvas}.setColor(\"{$this->_brush->Color}\");\n";
            $this->_brush->resetModified();
            $this->_pen->modified();
        }
    }

    /**
     * Force pen color if modified.
     */
    protected function forcePen(): void
    {
        if ($this->_pen->isModified()) {
            echo "{$this->_canvas}.setStroke({$this->_pen->Width});\n";
            echo "{$this->_canvas}.setColor(\"{$this->_pen->Color}\");\n";
            $this->_pen->resetModified();
            $this->_brush->modified();
        }
    }

    /**
     * Force font settings.
     */
    protected function forceFont(): void
    {
        $style = $this->_font->Style;
        $styleStr = $style instanceof \VCL\Graphics\Enums\FontStyle ? $style->value : (string)$style;
        echo "{$this->_canvas}.setFont(\"{$this->_font->Family}\", \"{$this->_font->Size}\", \"{$styleStr}\");\n";
        if ($this->_font->Color !== '') {
            echo "{$this->_canvas}.setColor(\"{$this->_font->Color}\");\n";
        }
    }

    /**
     * Initialize the JavaScript graphics library.
     */
    public function initLibrary(): void
    {
        if (!defined('COMMON_JS')) {
            echo "<script type=\"text/javascript\" src=\"" . (defined('VCL_HTTP_PATH') ? VCL_HTTP_PATH : '') . "/js/common.js\"></script>\n";
            define('COMMON_JS', 1);
        }

        if (!defined('JSCANVAS')) {
            echo "<script type=\"text/javascript\" src=\"" . (defined('VCL_HTTP_PATH') ? VCL_HTTP_PATH : '') . "/walterzorn/wz_jsgraphics.js\"></script>\n";
            define('JSCANVAS', 1);
        }

        if ($this->_owner !== null && property_exists($this->_owner, 'Name')) {
            $this->setCanvasProperties($this->_owner->Name);
        }
    }

    /**
     * Set canvas properties.
     */
    public function setCanvasProperties(string $name): void
    {
        $this->_canvas = $name . '_Canvas';
        $this->_object = $name;
    }

    /**
     * Begin drawing cycle.
     */
    public function beginDraw(): void
    {
        echo "<script type=\"text/javascript\">\n";
        echo " var cnv=findObj('{$this->_object}');\n";
        echo " if (cnv==null) cnv=findObj('{$this->_object}_outer');\n";
        echo "  var {$this->_canvas} = new jsGraphics(cnv);\n";
        $this->_canvas = '  ' . $this->_canvas;
    }

    /**
     * End drawing cycle.
     */
    public function endDraw(): void
    {
        $this->paint();
        echo "</script>\n";
    }

    /**
     * Draw a line.
     */
    public function line(int $x1, int $y1, int $x2, int $y2): void
    {
        $this->forcePen();
        echo "{$this->_canvas}.drawLine({$x1}, {$y1}, {$x2}, {$y2});\n";
    }

    /**
     * Draw an ellipse.
     */
    public function ellipse(int $x1, int $y1, int $x2, int $y2): void
    {
        $this->forceBrush();
        $w = $x2 - $x1 + 1;
        $h = $y2 - $y1 + 1;
        echo "{$this->_canvas}.fillEllipse(" . ($x1 + 1) . ", " . ($y1 + 1) . ", {$w}, {$h});\n";
        $this->forcePen();
        echo "{$this->_canvas}.drawEllipse({$x1}, {$y1}, {$w}, {$h});\n";
    }

    /**
     * Draw a rectangle.
     */
    public function rectangle(int $x1, int $y1, int $x2, int $y2): void
    {
        $w = $x2 - $x1 + 1;
        $h = $y2 - $y1 + 1;
        $this->forceBrush();
        echo "{$this->_canvas}.fillRect({$x1}, {$y1}, {$w}, {$h});\n";
        $this->forcePen();
        echo "{$this->_canvas}.drawRect({$x1}, {$y1}, {$w}, {$h});\n";
    }

    /**
     * Fill a rectangle.
     */
    public function fillRect(int $x1, int $y1, int $x2, int $y2): void
    {
        $this->forceBrush();
        echo "{$this->_canvas}.fillRect({$x1}, {$y1}, " . ($x2 - $x1) . ", " . ($y2 - $y1) . ");\n";
    }

    /**
     * Draw a frame rectangle.
     */
    public function frameRect(int $x1, int $y1, int $x2, int $y2): void
    {
        $this->forcePen();
        $this->forceBrush();
        echo "{$this->_canvas}.drawRect({$x1}, {$y1}, " . ($x2 - $x1 + 1) . ", " . ($y2 - $y1 + 1) . ");\n";
    }

    /**
     * Draw a polygon.
     */
    public function polygon(array $points): void
    {
        $this->forceBrush();
        $xPoints = [];
        $yPoints = [];
        $count = count($points);
        for ($i = 0; $i < $count; $i += 2) {
            $xPoints[] = $points[$i];
            $yPoints[] = $points[$i + 1];
        }
        echo "  var Xpoints = new Array(" . implode(',', $xPoints) . ");\n";
        echo "  var Ypoints = new Array(" . implode(',', $yPoints) . ");\n";
        echo "{$this->_canvas}.fillPolygon(Xpoints, Ypoints);\n";
        $this->forcePen();
        echo "{$this->_canvas}.drawPolygon(Xpoints, Ypoints);\n";
    }

    /**
     * Draw a polyline.
     */
    public function polyline(array $points): void
    {
        $this->forcePen();
        $xPoints = [];
        $yPoints = [];
        $count = count($points);
        for ($i = 0; $i < $count; $i += 2) {
            $xPoints[] = $points[$i];
            $yPoints[] = $points[$i + 1];
        }
        echo "  var Xpoints = new Array(" . implode(',', $xPoints) . ");\n";
        echo "  var Ypoints = new Array(" . implode(',', $yPoints) . ");\n";
        echo "{$this->_canvas}.drawPolyline(Xpoints, Ypoints);\n";
    }

    /**
     * Draw a rounded rectangle.
     */
    public function roundRect(int $x1, int $y1, int $x2, int $y2, int $w, int $h): void
    {
        $cx = $w / 2;
        $cy = $h / 2;
        $rw = $x2 - $x1 + 1;
        $rh = $y2 - $y1 + 1;
        $wp = $this->_pen->Width;

        $this->forceBrush();
        echo "{$this->_canvas}.fillRect(" . ($x1 + $cx) . ", {$y1}, " . ($rw - $w) . ", {$rh});\n";
        echo "{$this->_canvas}.fillRect({$x1}, " . ($y1 + $cy) . ", {$rw}, " . ($rh - $h) . ");\n";

        $this->forcePen();
        echo "{$this->_canvas}.drawLine(" . ($x1 + $cx) . ", {$y1}, " . ($x2 - $cx) . ", {$y1});\n";
        echo "{$this->_canvas}.drawLine(" . ($x1 + $cx) . ", {$y2}, " . ($x2 - $cx) . ", {$y2});\n";
        echo "{$this->_canvas}.drawLine({$x1}, " . ($y1 + $cy) . ", {$x1}, " . ($y2 - $cy) . ");\n";
        echo "{$this->_canvas}.drawLine({$x2}, " . ($y1 + $cy) . ", {$x2}, " . ($y2 - $cy) . ");\n";
    }

    /**
     * Draw an image stretched to fit.
     */
    public function stretchDraw(int $x1, int $y1, int $x2, int $y2, string $image): void
    {
        echo "{$this->_canvas}.drawImage(\"{$image}\", {$x1}, {$y1}, " . ($x2 - $x1 + 1) . ", " . ($y2 - $y1 + 1) . ");\n";
    }

    /**
     * Draw text at position.
     */
    public function textOut(int $x, int $y, string $text): void
    {
        $this->forceFont();
        echo "{$this->_canvas}.drawString(\"{$text}\", {$x}, {$y});\n";
    }

    /**
     * Draw a bevel rectangle.
     */
    public function bevelRect(int $x1, int $y1, int $x2, int $y2, string $color1, string $color2): void
    {
        $this->forcePen();
        echo "{$this->_canvas}.setColor(\"{$color1}\");\n";
        echo "{$this->_canvas}.drawLine({$x1}, {$y2}, {$x1}, {$y1});\n";
        echo "{$this->_canvas}.drawLine({$x1}, {$y1}, {$x2}, {$y1});\n";
        echo "{$this->_canvas}.setColor(\"{$color2}\");\n";
        echo "{$this->_canvas}.drawLine({$x2}, {$y1}, {$x2}, {$y2});\n";
        echo "{$this->_canvas}.drawLine({$x2}, {$y2}, {$x1}, {$y2});\n";
    }

    /**
     * Draw a colored line.
     */
    public function bevelLine(string $color, int $x1, int $y1, int $x2, int $y2): void
    {
        $this->forcePen();
        echo "{$this->_canvas}.setColor(\"{$color}\");\n";
        echo "{$this->_canvas}.drawLine({$x1}, {$y1}, {$x2}, {$y2});\n";
    }

    /**
     * Clear the canvas.
     */
    public function clear(): void
    {
        echo "{$this->_canvas}.clear();\n";
    }

    /**
     * Paint the canvas.
     */
    public function paint(): void
    {
        echo "{$this->_canvas}.paint();\n";
    }

    // Legacy getters/setters
    public function getBrush(): Brush { return $this->_brush; }
    public function setBrush(Brush $value): void { $this->_brush = $value; }

    public function getFont(): Font { return $this->_font; }
    public function setFont(Font $value): void { $this->_font = $value; }

    public function getPen(): Pen { return $this->_pen; }
    public function setPen(Pen $value): void { $this->_pen = $value; }
}

/**
 * Create color from hex string.
 */
function colorFromHex(\GdImage $img, string $hexColor): int
{
    while (strlen($hexColor) > 6) {
        $hexColor = substr($hexColor, 1);
    }
    sscanf($hexColor, "%2x%2x%2x", $red, $green, $blue);
    return imagecolorallocate($img, $red, $green, $blue);
}

/**
 * Create pen style pattern.
 */
function createPenStyle(\GdImage $img, string $penStyle, string $baseColor, string $bgColor): array
{
    $b = colorFromHex($img, $bgColor);
    $w = colorFromHex($img, $baseColor);

    return match($penStyle) {
        'psDash' => [$w, $w, $w, $w, $b, $b, $b, $b],
        'psDashDot' => [$w, $w, $w, $w, $b, $b, $w, $b, $b],
        'psDot' => [$w, $b, $b, $w, $b, $b],
        'psDashDotDot' => [$w, $w, $w, $w, $b, $w, $b, $w, $b],
        default => [$w], // psSolid
    };
}
