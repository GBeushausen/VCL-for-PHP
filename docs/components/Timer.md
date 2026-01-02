# Timer

Non-visual component that fires events at specified intervals.

**Namespace:** `VCL\ExtCtrls`
**File:** `src/VCL/ExtCtrls/Timer.php`
**Extends:** `Component`

## Usage

```php
use VCL\ExtCtrls\Timer;

$timer = new Timer($this);
$timer->Name = "Timer1";
$timer->Interval = 1000;  // 1 second
$timer->Enabled = true;
$timer->jsOnTimer = "handleTimer";
```

## Properties

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `Interval` | `int` | `1000` | Interval in milliseconds |
| `Enabled` | `bool` | `true` | Enable/disable timer |
| `jsOnTimer` | `?string` | `null` | JavaScript function name to call |

## JavaScript Handler

The timer calls a JavaScript function on each interval. Define the function in your page:

```php
class MyPage extends Page
{
    public ?Timer $Timer1 = null;

    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);

        $this->Timer1 = new Timer($this);
        $this->Timer1->Name = "Timer1";
        $this->Timer1->Interval = 5000;  // 5 seconds
        $this->Timer1->Enabled = true;
        $this->Timer1->jsOnTimer = "updateClock";
    }
}
```

In your JavaScript (add via header or separate file):

```javascript
function updateClock(event) {
    var now = new Date();
    document.getElementById('clock').innerHTML = now.toLocaleTimeString();
}
```

## Generated JavaScript

The Timer component generates JavaScript similar to:

```javascript
var Timer1_TimerID = null;

function Timer1_DisableTimer() {
    if (Timer1_TimerID) {
        clearTimeout(Timer1_TimerID);
        Timer1_TimerID = null;
    }
}

function Timer1_Event() {
    var event = {};
    if (Timer1_TimerID) {
        Timer1_DisableTimer();
        updateClock(event);
        Timer1_EnableTimer();
    }
}

function Timer1_EnableTimer() {
    Timer1_TimerID = self.setTimeout("Timer1_Event()", 1000);
}
```

## Notes

- Timer runs client-side in the browser
- Timer stops on page navigation/reload
- Use `Enabled = false` to stop the timer
- Minimum interval is determined by browser capabilities
