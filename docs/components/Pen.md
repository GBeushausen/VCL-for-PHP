# Pen

Used to draw lines or outline shapes on a canvas.

**Namespace:** `VCL\Graphics`
**File:** `src/VCL/Graphics/Pen.php`
**Extends:** `Persistent`

## Usage

```php
// Pen is typically accessed via Shape or Canvas
$this->Shape1->Pen->Color = "#000000";
$this->Shape1->Pen->Width = 2;
```

## Properties

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `Color` | `string` | `'#000000'` | Line color |
| `Width` | `int` | `1` | Line width in pixels |
| `Style` | `PenStyle` | `Solid` | Line style |

## PenStyle Values

| Value | Description |
|-------|-------------|
| `Solid` | Solid line |
| `Dash` | Dashed line |
| `Dot` | Dotted line |
| `DashDot` | Dash-dot pattern |
| `DashDotDot` | Dash-dot-dot pattern |

## Methods

| Method | Description |
|--------|-------------|
| `modified()` | Mark pen as modified |
| `isModified()` | Check if modified |
| `resetModified()` | Reset modified flag |
| `assignTo(Pen $dest)` | Copy pen to another |

## Example with Shape

```php
use VCL\ExtCtrls\Shape;

$this->Rectangle = new Shape($this);
$this->Rectangle->Name = "Rectangle";
$this->Rectangle->Parent = $this;
$this->Rectangle->Left = 20;
$this->Rectangle->Top = 20;
$this->Rectangle->Width = 150;
$this->Rectangle->Height = 100;
$this->Rectangle->Shape = "stRectangle";

// Configure outline
$this->Rectangle->Pen->Color = "#2c3e50";
$this->Rectangle->Pen->Width = 3;
$this->Rectangle->Pen->Style = "psDash";
```

## Example with Canvas

```php
public function PaintBox1Paint(object $sender, Canvas $canvas): void
{
    // Thin black line
    $canvas->Pen->Color = "#000000";
    $canvas->Pen->Width = 1;
    $canvas->MoveTo(10, 10);
    $canvas->LineTo(200, 10);

    // Thick red line
    $canvas->Pen->Color = "#e74c3c";
    $canvas->Pen->Width = 5;
    $canvas->MoveTo(10, 30);
    $canvas->LineTo(200, 30);

    // Blue dashed line
    $canvas->Pen->Color = "#3498db";
    $canvas->Pen->Width = 2;
    $canvas->Pen->Style = "psDash";
    $canvas->MoveTo(10, 50);
    $canvas->LineTo(200, 50);
}
```

## Drawing Shapes with Pen

When drawing shapes, Pen controls the outline:

```php
$canvas->Pen->Color = "#000000";  // Black outline
$canvas->Pen->Width = 2;
$canvas->Brush->Color = "#FF0000";  // Red fill

// Rectangle with black outline and red fill
$canvas->Rectangle(10, 10, 100, 80);
```

## Notes

- Pen controls outline/stroke of shapes
- Width is in pixels (minimum 1)
- Used by Shape, Canvas, Bevel components
- Default color is black (#000000)
