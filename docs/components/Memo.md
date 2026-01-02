# Memo

Multi-line text input control.

**Namespace:** `VCL\StdCtrls`
**File:** `src/VCL/StdCtrls/Memo.php`
**Extends:** `FocusControl`

## Usage

```php
use VCL\StdCtrls\Memo;

$memo = new Memo($this);
$memo->Name = "Memo1";
$memo->Parent = $this;
$memo->Left = 20;
$memo->Top = 100;
$memo->Width = 350;
$memo->Height = 150;
```

## Properties

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `Text` | `string` | `''` | Full text content |
| `Lines` | `array` | `[]` | Text as array of lines |
| `Left` | `int` | `0` | X position |
| `Top` | `int` | `0` | Y position |
| `Width` | `int` | `200` | Width in pixels |
| `Height` | `int` | `100` | Height in pixels |
| `MaxLength` | `int` | `0` | Max characters (0 = unlimited) |
| `ReadOnly` | `bool` | `false` | Prevent editing |
| `Enabled` | `bool` | `true` | Enable/disable |
| `Visible` | `bool` | `true` | Show/hide |
| `WordWrap` | `bool` | `true` | Enable word wrapping |
| `ScrollBars` | `string` | `'ssNone'` | Scrollbar visibility |
| `Placeholder` | `string` | `''` | Placeholder text |

## ScrollBars Values

| Value | Description |
|-------|-------------|
| `ssNone` | No scrollbars |
| `ssVertical` | Vertical scrollbar only |
| `ssHorizontal` | Horizontal scrollbar only |
| `ssBoth` | Both scrollbars |

## Reading the Value

```php
public function SubmitClick(object $sender, array $params): void
{
    // As single string
    $text = $this->Memo1->Text;

    // As array of lines
    $lines = $this->Memo1->Lines;
}
```

## Example

From [demo_advanced.php](../../demo_advanced.php):

```php
$this->CommentMemo = new Memo($this);
$this->CommentMemo->Name = "CommentMemo";
$this->CommentMemo->Parent = $this;
$this->CommentMemo->Left = 150;
$this->CommentMemo->Top = 242;
$this->CommentMemo->Width = 350;
$this->CommentMemo->Height = 100;
```

## Generated HTML

```html
<textarea id="CommentMemo" name="CommentMemo"
          style="width: 100%; height: 100%; resize: both; ..."
          wrap="soft"></textarea>
```
