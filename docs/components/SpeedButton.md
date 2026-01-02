# SpeedButton

A button used to execute commands or set modes, commonly used in toolbars.

**Namespace:** `VCL\Buttons`
**File:** `src/VCL/Buttons/SpeedButton.php`
**Extends:** `BitBtn`

## Usage

```php
use VCL\Buttons\SpeedButton;

$button = new SpeedButton($this);
$button->Name = "SpeedBtn1";
$button->Parent = $this;
$button->Left = 20;
$button->Top = 10;
$button->ImageSource = "images/bold.png";
$button->GroupIndex = 1;
```

## Properties

Inherits all properties from [BitBtn](BitBtn.md) plus:

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `AllowAllUp` | `bool` | `false` | Allow all buttons in group to be up |
| `Down` | `bool` | `false` | Current toggle state |
| `Flat` | `bool` | `false` | Flat button appearance |
| `GroupIndex` | `int` | `0` | Button group (0 = no group) |
| `Width` | `int` | `25` | Width in pixels |
| `Height` | `int` | `25` | Height in pixels |

## Properties from BitBtn

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `Caption` | `string` | `''` | Button text |
| `ImageSource` | `string` | `''` | Path to button image |
| `ButtonLayout` | `string` | `'blImageLeft'` | Image position |
| `Spacing` | `int` | `4` | Space between image and caption |
| `OnClick` | `?string` | `null` | Click handler method name |

## Group Behavior

When `GroupIndex > 0`:
- Buttons with the same `GroupIndex` form a radio group
- Only one button in the group can be `Down` at a time
- Clicking a button makes it `Down` and raises others
- If `AllowAllUp = true`, clicking the down button raises it (no button down)
- If `AllowAllUp = false`, one button always remains down

## Example: Toolbar

```php
// Bold button
$this->BoldBtn = new SpeedButton($this);
$this->BoldBtn->Name = "BoldBtn";
$this->BoldBtn->Parent = $this->Toolbar;
$this->BoldBtn->Left = 0;
$this->BoldBtn->Top = 0;
$this->BoldBtn->ImageSource = "icons/bold.png";
$this->BoldBtn->Flat = true;
$this->BoldBtn->GroupIndex = 0;  // Toggle independently

// Italic button
$this->ItalicBtn = new SpeedButton($this);
$this->ItalicBtn->Name = "ItalicBtn";
$this->ItalicBtn->Parent = $this->Toolbar;
$this->ItalicBtn->Left = 28;
$this->ItalicBtn->Top = 0;
$this->ItalicBtn->ImageSource = "icons/italic.png";
$this->ItalicBtn->Flat = true;
$this->ItalicBtn->GroupIndex = 0;
```

## Example: Radio Group

```php
// Left align
$this->AlignLeft = new SpeedButton($this);
$this->AlignLeft->Name = "AlignLeft";
$this->AlignLeft->Parent = $this->Toolbar;
$this->AlignLeft->Left = 100;
$this->AlignLeft->ImageSource = "icons/align-left.png";
$this->AlignLeft->Flat = true;
$this->AlignLeft->GroupIndex = 1;  // Same group
$this->AlignLeft->Down = true;     // Default selection

// Center align
$this->AlignCenter = new SpeedButton($this);
$this->AlignCenter->Name = "AlignCenter";
$this->AlignCenter->Parent = $this->Toolbar;
$this->AlignCenter->Left = 128;
$this->AlignCenter->ImageSource = "icons/align-center.png";
$this->AlignCenter->Flat = true;
$this->AlignCenter->GroupIndex = 1;

// Right align
$this->AlignRight = new SpeedButton($this);
$this->AlignRight->Name = "AlignRight";
$this->AlignRight->Parent = $this->Toolbar;
$this->AlignRight->Left = 156;
$this->AlignRight->ImageSource = "icons/align-right.png";
$this->AlignRight->Flat = true;
$this->AlignRight->GroupIndex = 1;
```

## Reading the State

```php
public function SubmitClick(object $sender, array $params): void
{
    if ($this->BoldBtn->Down) {
        // Bold is enabled
    }

    if ($this->AlignLeft->Down) {
        // Left alignment selected
    }
}
```

## Generated HTML

```html
<input type="hidden" name="BoldBtnDown" id="BoldBtnDown" value="0" />
<button type="button" id="BoldBtn" name="BoldBtn"
        style="display: inline-flex; align-items: center; justify-content: center;
               width: 25px; height: 25px; background-color: buttonface;
               border: 1px solid transparent; cursor: pointer;">
    <img src="icons/bold.png" alt="" style="margin: 0 4px;" />
</button>
```

## Notes

- SpeedButton is always `type="button"` (never submits form directly)
- Flat buttons show border only on hover
- Down state is persisted via hidden form field
- JavaScript manages group radio behavior client-side
