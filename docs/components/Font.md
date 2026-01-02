# Font

Encapsulates font properties for text display.

**Namespace:** `VCL\Graphics`
**File:** `src/VCL/Graphics/Font.php`
**Extends:** `Persistent`

## Usage

```php
// Font is typically accessed via control's Font property
$this->Label1->Font->Family = "Arial";
$this->Label1->Font->Size = "14px";
$this->Label1->Font->Color = "#333333";
$this->Label1->Font->Weight = "bold";
```

## Properties

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `Family` | `string` | `'Verdana'` | Font family name |
| `Size` | `string` | `'10px'` | Font size with unit |
| `Color` | `string` | `''` | Text color |
| `Weight` | `string` | `''` | Font weight (normal, bold, 100-900) |
| `Style` | `FontStyle` | `Normal` | Font style (normal, italic, oblique) |
| `Align` | `TextAlign` | `None` | Text alignment |
| `Case` | `TextCase` | `None` | Text case transformation |
| `Variant` | `FontVariant` | `Normal` | Font variant (small-caps) |
| `LineHeight` | `string` | `''` | Line height |

## FontStyle Values

| Value | Description |
|-------|-------------|
| `Normal` | Normal style |
| `Italic` | Italic |
| `Oblique` | Oblique |

## TextAlign Values

| Value | Description |
|-------|-------------|
| `None` | No alignment set |
| `Left` | Left align |
| `Right` | Right align |
| `Center` | Center |
| `Justify` | Justify |

## TextCase Values

| Value | Description |
|-------|-------------|
| `None` | No transformation |
| `Uppercase` | UPPERCASE |
| `Lowercase` | lowercase |
| `Capitalize` | Capitalize Each Word |

## Methods

| Method | Description |
|--------|-------------|
| `readFontString()` | Generate CSS font string |
| `startUpdate()` | Begin batch update |
| `endUpdate()` | End batch update |
| `assignTo(Font $dest)` | Copy font to another |

## Example

```php
// Configure label font
$this->Title = new Label($this);
$this->Title->Name = "Title";
$this->Title->Parent = $this;
$this->Title->Caption = "Welcome";
$this->Title->Font->Family = "Georgia, serif";
$this->Title->Font->Size = "24px";
$this->Title->Font->Color = "#2c3e50";
$this->Title->Font->Weight = "bold";
$this->Title->Font->Align = "Center";
```

## Batch Updates

For multiple changes, use batch mode to avoid multiple redraws:

```php
$this->Label1->Font->startUpdate();
$this->Label1->Font->Family = "Arial";
$this->Label1->Font->Size = "16px";
$this->Label1->Font->Color = "red";
$this->Label1->Font->Weight = "bold";
$this->Label1->Font->endUpdate();
```

## Generated CSS

```css
font-family: Georgia, serif;
font-size: 24px;
color: #2c3e50;
font-weight: bold;
text-align: center;
```

## Notes

- Font is a sub-object of controls, not standalone
- Changes to Font auto-update parent control
- Use CSS units for Size (px, em, rem, pt)
- Color accepts any CSS color value
