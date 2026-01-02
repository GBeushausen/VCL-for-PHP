# TrackBar

A slider control for selecting values by dragging.

**Namespace:** `VCL\ComCtrls`
**File:** `src/VCL/ComCtrls/TrackBar.php`
**Extends:** `Control`

## Usage

```php
use VCL\ComCtrls\TrackBar;

$track = new TrackBar($this);
$track->Name = "TrackBar1";
$track->Parent = $this;
$track->Left = 20;
$track->Top = 100;
$track->Width = 150;
$track->Height = 25;
$track->MinPosition = 0;
$track->MaxPosition = 100;
$track->Position = 50;
```

## Properties

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `Position` | `int` | `0` | Current slider value |
| `MinPosition` | `int` | `0` | Minimum value |
| `MaxPosition` | `int` | `10` | Maximum value |
| `Orientation` | `string` | `'tbHorizontal'` | Slider orientation |
| `Left` | `int` | `0` | X position |
| `Top` | `int` | `0` | Y position |
| `Width` | `int` | `150` | Width in pixels |
| `Height` | `int` | `25` | Height in pixels |

## Orientation Values

| Value | Description |
|-------|-------------|
| `tbHorizontal` | Horizontal slider |
| `tbVertical` | Vertical slider |

## Events

| Property | Type | Description |
|----------|------|-------------|
| `jsOnChange` | `?string` | JavaScript function called when value changes |

## Reading the Value

```php
public function SubmitClick(object $sender, array $params): void
{
    $value = $this->TrackBar1->Position;
    // Process slider value...
}
```

## Example

```php
$this->VolumeSlider = new TrackBar($this);
$this->VolumeSlider->Name = "VolumeSlider";
$this->VolumeSlider->Parent = $this;
$this->VolumeSlider->Left = 20;
$this->VolumeSlider->Top = 50;
$this->VolumeSlider->Width = 200;
$this->VolumeSlider->MinPosition = 0;
$this->VolumeSlider->MaxPosition = 100;
$this->VolumeSlider->Position = 75;
```

## Generated HTML

Uses HTML5 `<input type="range">`:

```html
<input type="hidden" id="VolumeSlider_position" name="VolumeSlider_position" value="75" />
<input type="range" id="VolumeSlider" name="VolumeSlider_input"
       min="0" max="100" value="75"
       style="width:200px;"
       oninput="document.getElementById('VolumeSlider_position').value=this.value;"
       onchange="document.getElementById('VolumeSlider_position').value=this.value;" />
```

## Notes

- Position value is automatically clamped between MinPosition and MaxPosition
- Changing orientation swaps width and height when appropriate
- Value is persisted via hidden form field for form submission
