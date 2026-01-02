# Panel

Container control that can hold other controls.

**Namespace:** `VCL\ExtCtrls`
**File:** `src/VCL/ExtCtrls/Panel.php`
**Extends:** `CustomPanel`

## Usage

```php
use VCL\ExtCtrls\Panel;

$panel = new Panel($this);
$panel->Name = "Panel1";
$panel->Parent = $this;
$panel->Left = 20;
$panel->Top = 20;
$panel->Width = 300;
$panel->Height = 200;
```

## Properties

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `Left` | `int` | `0` | X position |
| `Top` | `int` | `0` | Y position |
| `Width` | `int` | varies | Width in pixels |
| `Height` | `int` | varies | Height in pixels |
| `Color` | `string` | `''` | Background color |
| `Background` | `string` | `''` | Background image path |
| `BackgroundRepeat` | `string` | `''` | CSS background-repeat value |
| `BackgroundPosition` | `string` | `''` | CSS background-position value |
| `BorderWidth` | `int` | `0` | Border width in pixels |
| `BorderColor` | `string` | `''` | Border color |
| `Caption` | `string` | `''` | Panel text |
| `Visible` | `bool` | `true` | Show/hide |
| `Enabled` | `bool` | `true` | Enable/disable |
| `Include` | `string` | `''` | PHP file to include inside panel |
| `Dynamic` | `bool` | `false` | Dynamic content loading |

## Adding Child Controls

Set the `Parent` property of child controls to the panel:

```php
$panel = new Panel($this);
$panel->Name = "Panel1";
$panel->Parent = $this;
$panel->Left = 20;
$panel->Top = 20;
$panel->Width = 300;
$panel->Height = 200;

// Add label inside panel
$label = new Label($this);
$label->Name = "Label1";
$label->Parent = $panel;  // Parent is the panel
$label->Left = 10;        // Position relative to panel
$label->Top = 10;
$label->Caption = "Inside panel";

// Add button inside panel
$button = new Button($this);
$button->Name = "Button1";
$button->Parent = $panel;
$button->Left = 10;
$button->Top = 40;
$button->Caption = "Click";
```

## Generated HTML

```html
<div id="Panel1_outer" style="position: absolute; left: 20px; top: 20px; ...">
    <div id="Panel1" style="width: 300px; height: 200px; ...">
        <!-- Child controls rendered here -->
    </div>
</div>
```
