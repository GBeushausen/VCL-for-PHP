# Brush

Represents the color and pattern used to fill solid shapes.

**Namespace:** `VCL\Graphics`
**File:** `src/VCL/Graphics/Brush.php`
**Extends:** `Persistent`

## Usage

```php
// Brush is typically accessed via Shape or Canvas
$this->Shape1->Brush->Color = "#FF0000";
```

## Properties

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `Color` | `string` | `'#FFFFFF'` | Fill color |

## Methods

| Method | Description |
|--------|-------------|
| `modified()` | Mark brush as modified |
| `isModified()` | Check if modified |
| `resetModified()` | Reset modified flag |
| `assignTo(Brush $dest)` | Copy brush to another |

## Example with Shape

```php
use VCL\ExtCtrls\Shape;

$this->Circle = new Shape($this);
$this->Circle->Name = "Circle";
$this->Circle->Parent = $this;
$this->Circle->Left = 20;
$this->Circle->Top = 20;
$this->Circle->Width = 100;
$this->Circle->Height = 100;
$this->Circle->Shape = "stCircle";

// Set fill color
$this->Circle->Brush->Color = "#3498db";  // Blue fill
```

## Example with Canvas

```php
public function PaintBox1Paint(object $sender, Canvas $canvas): void
{
    // Set brush for filling
    $canvas->Brush->Color = "#e74c3c";  // Red

    // Filled rectangle
    $canvas->FillRect(10, 10, 100, 80);

    // Change color
    $canvas->Brush->Color = "#2ecc71";  // Green

    // Another filled shape
    $canvas->FillRect(120, 10, 210, 80);
}
```

## Color Values

Accepts any CSS color format:

```php
$brush->Color = "#FF0000";          // Hex
$brush->Color = "#F00";             // Short hex
$brush->Color = "rgb(255, 0, 0)";   // RGB
$brush->Color = "red";              // Named color
$brush->Color = "rgba(255,0,0,0.5)"; // RGBA with transparency
```

## Notes

- Brush controls fill color of shapes
- Used by Shape, Canvas, and drawing operations
- Default color is white (#FFFFFF)
- Part of the Graphics subsystem
