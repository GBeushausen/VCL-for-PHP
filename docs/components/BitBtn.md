# BitBtn

A push button control that can include a bitmap image on its face.

**Namespace:** `VCL\Buttons`
**File:** `src/VCL/Buttons/BitBtn.php`
**Extends:** `QWidget`

## Usage

```php
use VCL\Buttons\BitBtn;

$button = new BitBtn($this);
$button->Name = "BitBtn1";
$button->Parent = $this;
$button->Left = 20;
$button->Top = 100;
$button->Caption = "Save";
$button->ImageSource = "images/save.png";
$button->OnClick = "SaveClick";
```

## Properties

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `Caption` | `string` | `''` | Button text |
| `ImageSource` | `string` | `''` | Path to button image |
| `ImageDisabled` | `string` | `''` | Image when disabled |
| `ImageClicked` | `string` | `''` | Image when clicked |
| `ButtonLayout` | `string` | `'blImageLeft'` | Image position relative to caption |
| `Kind` | `string` | `'bkCustom'` | Predefined button kind |
| `ButtonType` | `string` | `'btSubmit'` | HTML button type |
| `Spacing` | `int` | `4` | Space between image and caption |
| `Default` | `bool` | `false` | Default button for form |
| `Cancel` | `bool` | `false` | Cancel button for form |
| `Left` | `int` | `0` | X position |
| `Top` | `int` | `0` | Y position |
| `Width` | `int` | `75` | Width in pixels |
| `Height` | `int` | `25` | Height in pixels |

## ButtonLayout Values

| Value | Description |
|-------|-------------|
| `blImageLeft` | Image to the left of caption |
| `blImageRight` | Image to the right of caption |
| `blImageTop` | Image above caption |
| `blImageBottom` | Image below caption |

## Kind Values

| Value | Description |
|-------|-------------|
| `bkCustom` | Custom button (default) |
| `bkOK` | OK button with checkmark |
| `bkCancel` | Cancel button with X |
| `bkYes` | Yes button |
| `bkNo` | No button |
| `bkHelp` | Help button |
| `bkClose` | Close button |
| `bkAbort` | Abort button |
| `bkRetry` | Retry button |
| `bkIgnore` | Ignore button |
| `bkAll` | All button |

## ButtonType Values

| Value | Description |
|-------|-------------|
| `btSubmit` | Submit form (default) |
| `btReset` | Reset form |
| `btButton` | Regular button (no form action) |

## Events

| Property | Type | Description |
|----------|------|-------------|
| `OnClick` | `?string` | Server-side click handler method name |

## Example

```php
class MyPage extends Page
{
    public ?BitBtn $SaveBtn = null;

    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);

        $this->SaveBtn = new BitBtn($this);
        $this->SaveBtn->Name = "SaveBtn";
        $this->SaveBtn->Parent = $this;
        $this->SaveBtn->Left = 20;
        $this->SaveBtn->Top = 200;
        $this->SaveBtn->Width = 100;
        $this->SaveBtn->Height = 30;
        $this->SaveBtn->Caption = "Save";
        $this->SaveBtn->ImageSource = "icons/save.png";
        $this->SaveBtn->ButtonLayout = "blImageLeft";
        $this->SaveBtn->OnClick = "SaveBtnClick";
    }

    public function SaveBtnClick(object $sender, array $params): void
    {
        // Save logic here
    }
}
```

## Generated HTML

```html
<button type="submit" id="SaveBtn" name="SaveBtn"
        style="display: inline-flex; align-items: center; justify-content: center;
               flex-direction: row; width: 100px; height: 30px;">
    <img src="icons/save.png" alt="" style="margin: 0 4px;" />
    <span>Save</span>
</button>
<input type="hidden" id="SaveBtn_event" name="SaveBtn_event" value="" />
```

## Notes

- Uses flexbox layout for image/caption positioning
- Supports different images for normal, disabled, and clicked states
- Event handling uses hidden field to track which button was clicked
