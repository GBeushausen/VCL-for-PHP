# Edit

Single-line text input control.

**Namespace:** `VCL\StdCtrls`
**File:** `src/VCL/StdCtrls/Edit.php`
**Extends:** `CustomEdit`

## Usage

```php
use VCL\StdCtrls\Edit;

$edit = new Edit($this);
$edit->Name = "Edit1";
$edit->Parent = $this;
$edit->Left = 20;
$edit->Top = 50;
$edit->Width = 200;
```

## Properties

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `Text` | `string` | `''` | Input text value |
| `Left` | `int` | `0` | X position in pixels |
| `Top` | `int` | `0` | Y position in pixels |
| `Width` | `int` | `121` | Width in pixels |
| `Height` | `int` | `21` | Height in pixels |
| `MaxLength` | `int` | `0` | Max characters (0 = unlimited) |
| `IsPassword` | `bool` | `false` | Mask input as password |
| `ReadOnly` | `bool` | `false` | Prevent editing |
| `Enabled` | `bool` | `true` | Enable/disable |
| `Visible` | `bool` | `true` | Show/hide |
| `TabOrder` | `int` | `0` | Tab order |
| `TabStop` | `bool` | `true` | Include in tab navigation |
| `CharCase` | `CharCase` | `ecNormal` | Character case conversion |
| `BorderStyle` | `BorderStyle` | `bsSingle` | Border style |
| `FilterInput` | `bool` | `true` | HTML-escape input |

## CharCase Values

| Value | Description |
|-------|-------------|
| `ecNormal` | No conversion |
| `ecUpperCase` | Convert to uppercase |
| `ecLowerCase` | Convert to lowercase |

## Events

| Property | Type | Description |
|----------|------|-------------|
| `OnClick` | `?string` | Click handler |
| `OnDblClick` | `?string` | Double-click handler |
| `OnSubmit` | `?string` | Form submit handler |

## Reading the Value

After form submission (in event handler):

```php
public function Button1Click(object $sender, array $params): void
{
    $value = $this->Edit1->Text;
}
```

## Example

From [demo_simple.php](../../demo_simple.php):

```php
$this->Edit1 = new Edit($this);
$this->Edit1->Name = "Edit1";
$this->Edit1->Parent = $this;
$this->Edit1->Left = 20;
$this->Edit1->Top = 50;
$this->Edit1->Width = 200;
$this->Edit1->Text = "";
```

## Generated HTML

```html
<input type="text" id="Edit1" name="Edit1" value=""
       style="width:200px;" tabindex="0" />
```
