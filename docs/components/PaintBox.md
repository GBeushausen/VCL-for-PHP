# PaintBox

Provides a canvas for custom drawing.

**Namespace:** `VCL\ExtCtrls`
**File:** `src/VCL/ExtCtrls/PaintBox.php`
**Extends:** `Control`

## Usage

```php
use VCL\ExtCtrls\PaintBox;

$paint = new PaintBox($this);
$paint->Name = "PaintBox1";
$paint->Parent = $this;
$paint->Left = 20;
$paint->Top = 20;
$paint->Width = 200;
$paint->Height = 200;
$paint->OnPaint = "PaintBox1Paint";
```

## Properties

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `Canvas` | `Canvas` | | Drawing surface |
| `Left` | `int` | `0` | X position |
| `Top` | `int` | `0` | Y position |
| `Width` | `int` | `100` | Width in pixels |
| `Height` | `int` | `100` | Height in pixels |

## Events

| Property | Type | Description |
|----------|------|-------------|
| `OnPaint` | `?string` | Called when painting is needed |
| `OnClick` | `?string` | Click handler |
| `OnDblClick` | `?string` | Double-click handler |

## Drawing with Canvas

```php
class MyPage extends Page
{
    public ?PaintBox $PaintBox1 = null;

    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);

        $this->PaintBox1 = new PaintBox($this);
        $this->PaintBox1->Name = "PaintBox1";
        $this->PaintBox1->Parent = $this;
        $this->PaintBox1->Left = 20;
        $this->PaintBox1->Top = 20;
        $this->PaintBox1->Width = 300;
        $this->PaintBox1->Height = 200;
        $this->PaintBox1->OnPaint = "PaintBox1Paint";
    }

    public function PaintBox1Paint(object $sender, Canvas $canvas): void
    {
        // Set pen and brush
        $canvas->Pen->Color = "#000000";
        $canvas->Pen->Width = 2;
        $canvas->Brush->Color = "#FF0000";

        // Draw rectangle
        $canvas->Rectangle(10, 10, 100, 80);

        // Draw ellipse
        $canvas->Brush->Color = "#00FF00";
        $canvas->Ellipse(120, 10, 200, 80);

        // Draw line
        $canvas->MoveTo(10, 100);
        $canvas->LineTo(200, 150);
    }
}
```

## Canvas Methods

| Method | Description |
|--------|-------------|
| `Rectangle(x1, y1, x2, y2)` | Draw rectangle |
| `FillRect(x1, y1, x2, y2)` | Fill rectangle |
| `Ellipse(x1, y1, x2, y2)` | Draw ellipse |
| `RoundRect(x1, y1, x2, y2, rx, ry)` | Draw rounded rectangle |
| `MoveTo(x, y)` | Move drawing position |
| `LineTo(x, y)` | Draw line to position |
| `TextOut(x, y, text)` | Draw text |

## Notes

- Canvas uses JavaScript for browser-side drawing
- OnPaint is called each time the control needs to be drawn
- Unlike Image, PaintBox requires programmatic drawing
