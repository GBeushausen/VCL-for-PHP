# Shape

Draws simple geometric shapes on a form.

**Namespace:** `VCL\ExtCtrls`
**File:** `src/VCL/ExtCtrls/Shape.php`
**Extends:** `Control`

## Usage

```php
use VCL\ExtCtrls\Shape;

$shape = new Shape($this);
$shape->Name = "Shape1";
$shape->Parent = $this;
$shape->Left = 20;
$shape->Top = 20;
$shape->Width = 100;
$shape->Height = 100;
$shape->Shape = "stRectangle";
```

## Properties

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `Shape` | `string` | `'stRectangle'` | Type of shape to draw |
| `Pen` | `Pen` | | Outline properties |
| `Brush` | `Brush` | | Fill properties |
| `Left` | `int` | `0` | X position |
| `Top` | `int` | `0` | Y position |
| `Width` | `int` | `65` | Width in pixels |
| `Height` | `int` | `65` | Height in pixels |

## Shape Types

| Value | Description |
|-------|-------------|
| `stRectangle` | Rectangle |
| `stSquare` | Square (equal sides) |
| `stRoundRect` | Rounded rectangle |
| `stRoundSquare` | Rounded square |
| `stEllipse` | Ellipse |
| `stCircle` | Circle |

## Example

```php
// Red circle with black border
$this->Circle1 = new Shape($this);
$this->Circle1->Name = "Circle1";
$this->Circle1->Parent = $this;
$this->Circle1->Left = 20;
$this->Circle1->Top = 20;
$this->Circle1->Width = 100;
$this->Circle1->Height = 100;
$this->Circle1->Shape = "stCircle";
$this->Circle1->Brush->Color = "#FF0000";
$this->Circle1->Pen->Color = "#000000";
$this->Circle1->Pen->Width = 2;
```

## Notes

- Uses Canvas for drawing (JavaScript-based)
- Pen controls outline color and width
- Brush controls fill color
