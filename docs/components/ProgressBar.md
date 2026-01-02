# ProgressBar

Provides visual feedback about the progress of a procedure.

**Namespace:** `VCL\ComCtrls`
**File:** `src/VCL/ComCtrls/ProgressBar.php`
**Extends:** `CustomProgressBar`

## Usage

```php
use VCL\ComCtrls\ProgressBar;

$progress = new ProgressBar($this);
$progress->Name = "ProgressBar1";
$progress->Parent = $this;
$progress->Left = 20;
$progress->Top = 100;
$progress->Width = 200;
$progress->Height = 17;
$progress->Min = 0;
$progress->Max = 100;
$progress->Position = 50;
```

## Properties

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `Position` | `int` | `50` | Current progress value |
| `Min` | `int` | `0` | Minimum value |
| `Max` | `int` | `100` | Maximum value |
| `Step` | `int` | `10` | Step increment value |
| `Orientation` | `string` | `'pbHorizontal'` | Progress bar orientation |
| `Left` | `int` | `0` | X position |
| `Top` | `int` | `0` | Y position |
| `Width` | `int` | `200` | Width in pixels |
| `Height` | `int` | `17` | Height in pixels |

## Orientation Values

| Value | Description |
|-------|-------------|
| `pbHorizontal` | Horizontal progress bar |
| `pbVertical` | Vertical progress bar |

## Methods

| Method | Description |
|--------|-------------|
| `stepIt()` | Advance position by Step amount |
| `stepBy(int $value)` | Advance position by specified amount |

## Example

```php
$this->ProgressBar1 = new ProgressBar($this);
$this->ProgressBar1->Name = "ProgressBar1";
$this->ProgressBar1->Parent = $this;
$this->ProgressBar1->Left = 20;
$this->ProgressBar1->Top = 100;
$this->ProgressBar1->Width = 300;
$this->ProgressBar1->Min = 0;
$this->ProgressBar1->Max = 100;
$this->ProgressBar1->Position = 0;

// Update progress
$this->ProgressBar1->Position = 25;  // 25%
$this->ProgressBar1->stepIt();        // +10% = 35%
$this->ProgressBar1->stepBy(15);      // +15% = 50%
```

## Generated HTML

Uses HTML5 `<progress>` element:

```html
<progress id="ProgressBar1" value="50" max="100"
          style="width:200px;height:17px;">
    50%
</progress>
```
