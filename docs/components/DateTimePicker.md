# DateTimePicker

A control for entering dates or times with a popup calendar/time selector.

**Namespace:** `VCL\ComCtrls`
**File:** `src/VCL/ComCtrls/DateTimePicker.php`
**Extends:** `FocusControl`

## Usage

```php
use VCL\ComCtrls\DateTimePicker;

$picker = new DateTimePicker($this);
$picker->Name = "DatePicker1";
$picker->Parent = $this;
$picker->Left = 20;
$picker->Top = 100;
$picker->Width = 150;
$picker->ShowsTime = true;
```

## Properties

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `Date` | `string` | `''` | Selected date/time value |
| `ShowsTime` | `bool` | `true` | Include time selection |
| `DateFormat` | `string` | `'Y-m-d H:i'` | PHP date format |
| `TimeZone` | `string` | `'UTC'` | Timezone for date handling |
| `Left` | `int` | `0` | X position |
| `Top` | `int` | `0` | Y position |
| `Width` | `int` | `150` | Width in pixels |
| `Height` | `int` | `25` | Height in pixels |
| `Enabled` | `bool` | `true` | Enable/disable |
| `Visible` | `bool` | `true` | Show/hide |

## Events

| Property | Type | Description |
|----------|------|-------------|
| `jsOnUpdate` | `?string` | JavaScript function called when date changes |

## Reading the Value

```php
public function SubmitClick(object $sender, array $params): void
{
    $date = $this->DatePicker1->Date;
    // Format: 2024-01-15T14:30 or 2024-01-15 depending on ShowsTime
}
```

## Examples

### Date Only

```php
$this->BirthDate = new DateTimePicker($this);
$this->BirthDate->Name = "BirthDate";
$this->BirthDate->Parent = $this;
$this->BirthDate->Left = 150;
$this->BirthDate->Top = 100;
$this->BirthDate->ShowsTime = false;
```

### Date and Time

```php
$this->AppointmentTime = new DateTimePicker($this);
$this->AppointmentTime->Name = "AppointmentTime";
$this->AppointmentTime->Parent = $this;
$this->AppointmentTime->Left = 150;
$this->AppointmentTime->Top = 150;
$this->AppointmentTime->ShowsTime = true;
$this->AppointmentTime->Date = 'now';  // Current date/time
```

### With JavaScript Handler

```php
$this->EventDate = new DateTimePicker($this);
$this->EventDate->Name = "EventDate";
$this->EventDate->Parent = $this;
$this->EventDate->Left = 150;
$this->EventDate->Top = 200;
$this->EventDate->jsOnUpdate = "dateChanged";
```

```javascript
function dateChanged(event) {
    var selectedDate = event.target.value;
    console.log("Date selected: " + selectedDate);
}
```

## Generated HTML

With `ShowsTime = true`:
```html
<input type="datetime-local" id="AppointmentTime" name="AppointmentTime"
       value="2024-01-15T14:30" style="width:150px;" />
```

With `ShowsTime = false`:
```html
<input type="date" id="BirthDate" name="BirthDate"
       value="2024-01-15" style="width:150px;" />
```

## Notes

- Uses native HTML5 date/datetime-local inputs
- Browser provides native calendar/time picker UI
- Date format for HTML5 is always `Y-m-d` or `Y-m-d\TH:i`
- Setting `Date = 'now'` uses the current date/time
