# ComboBox

Dropdown selection control.

**Namespace:** `VCL\StdCtrls`
**File:** `src/VCL/StdCtrls/ComboBox.php`
**Extends:** `FocusControl`

## Usage

```php
use VCL\StdCtrls\ComboBox;

$combo = new ComboBox($this);
$combo->Name = "ComboBox1";
$combo->Parent = $this;
$combo->Left = 20;
$combo->Top = 100;
$combo->Width = 200;
$combo->Items = ["Option 1", "Option 2", "Option 3"];
```

## Properties

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `Items` | `array` | `[]` | List of options |
| `ItemIndex` | `int` | `-1` | Selected index (-1 = none) |
| `Text` | `string` | `''` | Selected text value |
| `Sorted` | `bool` | `false` | Sort items alphabetically |
| `Left` | `int` | `0` | X position |
| `Top` | `int` | `0` | Y position |
| `Width` | `int` | `150` | Width in pixels |
| `Height` | `int` | `25` | Height in pixels |
| `Enabled` | `bool` | `true` | Enable/disable |
| `Visible` | `bool` | `true` | Show/hide |

## Events

| Property | Type | Description |
|----------|------|-------------|
| `OnChange` | `?string` | Selection change handler |

## Methods

| Method | Description |
|--------|-------------|
| `addItem(string $item)` | Add item to list |
| `clear()` | Remove all items |

## Reading the Value

```php
public function SubmitClick(object $sender, array $params): void
{
    $index = $this->ComboBox1->ItemIndex;
    if ($index >= 0) {
        $selected = $this->ComboBox1->Items[$index];
        // or
        $selected = $this->ComboBox1->Text;
    }
}
```

## Example

From [demo_advanced.php](../../demo_advanced.php):

```php
$this->CountryCombo = new ComboBox($this);
$this->CountryCombo->Name = "CountryCombo";
$this->CountryCombo->Parent = $this;
$this->CountryCombo->Left = 150;
$this->CountryCombo->Top = 172;
$this->CountryCombo->Width = 250;
$this->CountryCombo->Items = [
    "Deutschland",
    "Oesterreich",
    "Schweiz",
    "Liechtenstein",
    "Luxemburg"
];
```

## Generated HTML

```html
<select id="CountryCombo" name="CountryCombo" style="width: 100%; height: 100%; ...">
    <option value="Deutschland">Deutschland</option>
    <option value="Oesterreich">Oesterreich</option>
    <option value="Schweiz" selected>Schweiz</option>
</select>
```
