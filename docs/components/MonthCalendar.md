# MonthCalendar

Displays a calendar for date selection.

**Namespace:** `VCL\ComCtrls`
**File:** `src/VCL/ComCtrls/MonthCalendar.php`
**Extends:** `FocusControl`

## Usage

```php
use VCL\ComCtrls\MonthCalendar;

$calendar = new MonthCalendar($this);
$calendar->Name = "Calendar1";
$calendar->Parent = $this;
$calendar->Left = 20;
$calendar->Top = 20;
$calendar->Width = 200;
$calendar->Height = 200;
```

## Properties

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `Date` | `string` | `''` | Selected date |
| `ShowsTime` | `bool` | `true` | Include time selection |
| `FirstDay` | `int` | `1` | First day of week (0=Sun, 1=Mon) |
| `DateFormat` | `string` | `'%m-%d-%Y %I:%M'` | Display format |
| `TimeZone` | `string` | `'UTC'` | Timezone |
| `Left` | `int` | `0` | X position |
| `Top` | `int` | `0` | Y position |
| `Width` | `int` | `200` | Width in pixels |
| `Height` | `int` | `200` | Height in pixels |

## Events

| Property | Type | Description |
|----------|------|-------------|
| `jsOnUpdate` | `?string` | JavaScript function called on date change |

## Reading the Value

```php
public function SubmitClick(object $sender, array $params): void
{
    $selectedDate = $this->Calendar1->Date;
    // Process the date...
}
```

## Example

```php
$this->BirthCalendar = new MonthCalendar($this);
$this->BirthCalendar->Name = "BirthCalendar";
$this->BirthCalendar->Parent = $this;
$this->BirthCalendar->Left = 20;
$this->BirthCalendar->Top = 50;
$this->BirthCalendar->Width = 250;
$this->BirthCalendar->Height = 250;
$this->BirthCalendar->ShowsTime = false;
$this->BirthCalendar->FirstDay = 1;  // Monday
```

## Generated HTML

Uses HTML5 date/datetime-local input:

```html
<input type="hidden" name="Calendar1_date" id="Calendar1_date" value="" />
<div id="Calendar1_container" style="width:200px;height:200px;">
    <input type="date" id="Calendar1_input" value="2024-01-15"
           style="width:100%;height:100%;"
           onchange="Calendar1_update(event)" />
</div>
```

## Notes

- Uses native HTML5 date picker
- Similar to DateTimePicker but displays inline
- FirstDay property sets week start (0=Sunday, 1=Monday, etc.)
