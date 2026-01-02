# Bevel

Creates beveled boxes, frames, or lines.

**Namespace:** `VCL\ExtCtrls`
**File:** `src/VCL/ExtCtrls/Bevel.php`
**Extends:** `GraphicControl`

## Usage

```php
use VCL\ExtCtrls\Bevel;

$bevel = new Bevel($this);
$bevel->Name = "Bevel1";
$bevel->Parent = $this;
$bevel->Left = 20;
$bevel->Top = 20;
$bevel->Width = 200;
$bevel->Height = 100;
$bevel->Shape = "bsBox";
$bevel->BevelStyle = "bsLowered";
```

## Properties

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `Shape` | `string` | `'bsBox'` | Type of bevel shape |
| `BevelStyle` | `string` | `'bsLowered'` | Raised or lowered appearance |
| `Left` | `int` | `0` | X position |
| `Top` | `int` | `0` | Y position |
| `Width` | `int` | varies | Width in pixels |
| `Height` | `int` | varies | Height in pixels |

## Shape Types

| Value | Description |
|-------|-------------|
| `bsBox` | Box outline |
| `bsFrame` | Double-line frame |
| `bsTopLine` | Horizontal line at top |
| `bsBottomLine` | Horizontal line at bottom |
| `bsLeftLine` | Vertical line at left |
| `bsRightLine` | Vertical line at right |
| `bsSpacer` | Invisible spacer |

## BevelStyle Values

| Value | Description |
|-------|-------------|
| `bsLowered` | Sunken/inset appearance |
| `bsRaised` | Raised/outset appearance |

## Example

```php
// Sunken frame
$this->Frame1 = new Bevel($this);
$this->Frame1->Name = "Frame1";
$this->Frame1->Parent = $this;
$this->Frame1->Left = 10;
$this->Frame1->Top = 10;
$this->Frame1->Width = 300;
$this->Frame1->Height = 200;
$this->Frame1->Shape = "bsFrame";
$this->Frame1->BevelStyle = "bsLowered";

// Horizontal separator line
$this->Separator = new Bevel($this);
$this->Separator->Name = "Separator";
$this->Separator->Parent = $this;
$this->Separator->Left = 0;
$this->Separator->Top = 100;
$this->Separator->Width = 400;
$this->Separator->Height = 2;
$this->Separator->Shape = "bsTopLine";
```

## Notes

- Uses Canvas for drawing
- Creates 3D border effects
- Useful for visual grouping of controls
