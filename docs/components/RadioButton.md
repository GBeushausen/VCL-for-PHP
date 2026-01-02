# RadioButton

Radio button for single selection from a group.

**Namespace:** `VCL\StdCtrls`
**File:** `src/VCL/StdCtrls/RadioButton.php`
**Extends:** `FocusControl`

## Usage

```php
use VCL\StdCtrls\RadioButton;

$radio = new RadioButton($this);
$radio->Name = "Radio1";
$radio->Parent = $this;
$radio->Left = 20;
$radio->Top = 100;
$radio->Caption = "Option A";
$radio->Group = "options";
```

## Properties

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `Caption` | `string` | `''` | Label text |
| `Checked` | `bool` | `false` | Selected state |
| `Group` | `string` | `''` | Group name (radio buttons with same group are mutually exclusive) |
| `Left` | `int` | `0` | X position |
| `Top` | `int` | `0` | Y position |
| `Width` | `int` | `100` | Width in pixels |
| `Height` | `int` | `20` | Height in pixels |
| `Enabled` | `bool` | `true` | Enable/disable |
| `Visible` | `bool` | `true` | Show/hide |

## Grouping

RadioButtons with the same `Group` value form an exclusive group - only one can be selected:

```php
$this->GenderMale = new RadioButton($this);
$this->GenderMale->Name = "GenderMale";
$this->GenderMale->Caption = "Male";
$this->GenderMale->Group = "gender";

$this->GenderFemale = new RadioButton($this);
$this->GenderFemale->Name = "GenderFemale";
$this->GenderFemale->Caption = "Female";
$this->GenderFemale->Group = "gender";
```

## Reading the Value

```php
public function SubmitClick(object $sender, array $params): void
{
    if ($this->GenderMale->Checked) {
        $gender = "Male";
    } elseif ($this->GenderFemale->Checked) {
        $gender = "Female";
    }
}
```

## Example

From [demo_advanced.php](../../demo_advanced.php):

```php
$this->GenderMale = new RadioButton($this);
$this->GenderMale->Name = "GenderMale";
$this->GenderMale->Parent = $this;
$this->GenderMale->Left = 150;
$this->GenderMale->Top = 70;
$this->GenderMale->Caption = "Herr";
$this->GenderMale->Group = "gender";
```

## Generated HTML

```html
<span id="GenderMale_wrapper" style="display: inline-block">
    <input type="radio" id="GenderMale" name="gender" value="GenderMale" />
    <label for="GenderMale">Herr</label>
</span>
```

Note: The HTML `name` attribute uses the `Group` value, the `value` attribute uses the component `Name`.
