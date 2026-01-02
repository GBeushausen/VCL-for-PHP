# Button

Standard push button control.

**Namespace:** `VCL\StdCtrls`
**File:** `src/VCL/StdCtrls/Button.php`
**Extends:** `ButtonControl`

## Usage

```php
use VCL\StdCtrls\Button;

$button = new Button($this);
$button->Name = "Button1";
$button->Parent = $this;
$button->Left = 20;
$button->Top = 50;
$button->Caption = "Submit";
$button->OnClick = "Button1Click";
```

## Properties

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `Caption` | `string` | `''` | Button text |
| `Left` | `int` | `0` | X position in pixels |
| `Top` | `int` | `0` | Y position in pixels |
| `Width` | `int` | `75` | Width in pixels |
| `Height` | `int` | `25` | Height in pixels |
| `Enabled` | `bool` | `true` | Enable/disable button |
| `Visible` | `bool` | `true` | Show/hide button |
| `TabOrder` | `int` | `0` | Tab order |
| `TabStop` | `bool` | `true` | Include in tab navigation |
| `Default` | `bool` | `false` | Default button (Enter key) |
| `Cancel` | `bool` | `false` | Cancel button (Escape key) |
| `ButtonType` | `string` | `'btSubmit'` | Button type constant |

## Button Types

| Constant | HTML Type | Description |
|----------|-----------|-------------|
| `btSubmit` | `submit` | Submits the form (default) |
| `btReset` | `reset` | Resets form fields |
| `btButton` | `button` | Plain button, no default action |

## Events

| Property | Type | Description |
|----------|------|-------------|
| `OnClick` | `?string` | Server-side click handler method name |
| `OnSubmit` | `?string` | Server-side submit handler method name |

## Event Handler

```php
public function Button1Click(object $sender, array $params): void
{
    // Handle click event
}
```

## Example

From [demo_simple.php](../../demo_simple.php):

```php
$this->Button1 = new Button($this);
$this->Button1->Name = "Button1";
$this->Button1->Parent = $this;
$this->Button1->Left = 230;
$this->Button1->Top = 48;
$this->Button1->Caption = "Klick mich!";
$this->Button1->OnClick = "Button1Click";
```

## Generated HTML

```html
<input type="submit" id="Button1" name="Button1" value="Submit"
       style="height:25px;width:75px;" tabindex="0" />
```
