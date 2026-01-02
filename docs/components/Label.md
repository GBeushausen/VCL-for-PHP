# Label

Displays text that the user cannot edit.

**Namespace:** `VCL\StdCtrls`
**File:** `src/VCL/StdCtrls/Label.php`
**Extends:** `CustomLabel`

## Usage

```php
use VCL\StdCtrls\Label;

$label = new Label($this);
$label->Name = "Label1";
$label->Parent = $this;
$label->Left = 20;
$label->Top = 20;
$label->Caption = "Hello World";
```

## Properties

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `Caption` | `string` | `''` | Displayed text |
| `Left` | `int` | `0` | X position in pixels |
| `Top` | `int` | `0` | Y position in pixels |
| `Width` | `int` | `75` | Width in pixels |
| `Height` | `int` | `13` | Height in pixels |
| `Visible` | `bool` | `true` | Show/hide label |
| `Enabled` | `bool` | `true` | Enable/disable |
| `Font` | `Font` | - | Font settings object |
| `Color` | `string` | `''` | Background color |
| `Alignment` | `Anchors\|string` | `'agNone'` | Text alignment |
| `AutoSize` | `bool` | `true` | Auto-adjust size to content |
| `WordWrap` | `bool` | `false` | Enable word wrapping |
| `HtmlContent` | `bool` | `false` | Allow HTML in Caption (not escaped) |

## Font Properties

```php
$label->Font->Family = "Arial";
$label->Font->Size = "14px";
$label->Font->Color = "#333333";
$label->Font->Weight = "bold";
```

## Events

| Property | Type | Description |
|----------|------|-------------|
| `OnClick` | `?string` | Click handler method name |
| `OnDblClick` | `?string` | Double-click handler method name |

## Example

From [demo_simple.php](../../demo_simple.php):

```php
$this->OutputLabel = new Label($this);
$this->OutputLabel->Name = "OutputLabel";
$this->OutputLabel->Parent = $this;
$this->OutputLabel->Left = 20;
$this->OutputLabel->Top = 100;
$this->OutputLabel->Caption = "";
$this->OutputLabel->Font->Size = "14px";
$this->OutputLabel->Font->Color = "#0066cc";
```

## Generated HTML

```html
<div id="Label1_outer" style="position: absolute; left: 20px; top: 20px; ...">
    <div id="Label1" style="font-family: Verdana; font-size: 10px; ...">
        Hello World
    </div>
</div>
```
