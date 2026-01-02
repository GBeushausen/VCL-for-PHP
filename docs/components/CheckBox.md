# CheckBox

Checkbox control for boolean selections.

**Namespace:** `VCL\StdCtrls`
**File:** `src/VCL/StdCtrls/CheckBox.php`
**Extends:** `CustomCheckBox`

## Usage

```php
use VCL\StdCtrls\CheckBox;

$checkbox = new CheckBox($this);
$checkbox->Name = "CheckBox1";
$checkbox->Parent = $this;
$checkbox->Left = 20;
$checkbox->Top = 100;
$checkbox->Caption = "Accept terms";
```

## Properties

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `Caption` | `string` | `''` | Label text |
| `Checked` | `bool` | `false` | Checked state |
| `Left` | `int` | `0` | X position |
| `Top` | `int` | `0` | Y position |
| `Width` | `int` | `113` | Width in pixels |
| `Height` | `int` | `17` | Height in pixels |
| `Enabled` | `bool` | `true` | Enable/disable |
| `Visible` | `bool` | `true` | Show/hide |
| `TabOrder` | `int` | `0` | Tab order |
| `TabStop` | `bool` | `true` | Include in tab navigation |

## Events

| Property | Type | Description |
|----------|------|-------------|
| `OnClick` | `?string` | Click handler |

## Reading the Value

```php
public function SubmitClick(object $sender, array $params): void
{
    if ($this->CheckBox1->Checked) {
        // Checkbox is checked
    }
}
```

## Example

From [demo_advanced.php](../../demo_advanced.php):

```php
$this->NewsletterCheck = new CheckBox($this);
$this->NewsletterCheck->Name = "NewsletterCheck";
$this->NewsletterCheck->Parent = $this;
$this->NewsletterCheck->Left = 150;
$this->NewsletterCheck->Top = 210;
$this->NewsletterCheck->Caption = "Ja, ich moechte den Newsletter erhalten";
$this->NewsletterCheck->Width = 300;
```

## Generated HTML

```html
<span id="CheckBox1_wrapper" style="...">
    <input type="checkbox" id="CheckBox1" name="CheckBox1" value="on" tabindex="0" />
    <label for="CheckBox1">Accept terms</label>
</span>
```
