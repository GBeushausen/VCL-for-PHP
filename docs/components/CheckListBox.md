# CheckListBox

A list control where each item has a checkbox next to it.

**Namespace:** `VCL\StdCtrls`
**File:** `src/VCL/StdCtrls/CheckListBox.php`
**Extends:** `CustomCheckListBox` → `FocusControl`

## Usage

```php
use VCL\StdCtrls\CheckListBox;

$checkList = new CheckListBox($this);
$checkList->Name = 'CheckListBox1';
$checkList->Parent = $this;
$checkList->Left = 20;
$checkList->Top = 20;
$checkList->Width = 200;
$checkList->Height = 150;
$checkList->Items = ['Option 1', 'Option 2', 'Option 3', 'Option 4'];
$checkList->OnClick = 'CheckListBox1Click';
```

## Properties

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `Items` | `array` | `[]` | Array of items to display |
| `Checked` | `array` | `[]` | Array of checked states (key => bool) |
| `Columns` | `int` | `1` | Number of columns for layout |
| `BorderStyle` | `int` | `BS_SINGLE` | Border style (BS_NONE, BS_SINGLE) |
| `BorderWidth` | `int` | `1` | Border width in pixels |
| `BorderColor` | `string` | `'#CCCCCC'` | Border color |
| `Header` | `array` | `[]` | Array marking header items |
| `HeaderBackgroundColor` | `string` | `'#CCCCCC'` | Background color for header items |
| `HeaderColor` | `string` | `'#FFFFFF'` | Font color for header items |
| `TabOrder` | `int` | `0` | Tab navigation order |
| `TabStop` | `int` | `1` | Whether control can receive focus via Tab |

## Events

| Event | Description |
|-------|-------------|
| `OnClick` | Fires when an item is clicked |
| `OnSubmit` | Fires when the form is submitted |

## Methods

| Method | Description |
|--------|-------------|
| `addItem(mixed $item, mixed $key = null)` | Add an item to the list |
| `clear()` | Remove all items |
| `itemAtPos(mixed $key)` | Get item by key |
| `selectAll()` | Check all items |
| `deselectAll()` | Uncheck all items |
| `isItemChecked(int $index)` | Check if item is checked |
| `setItemChecked(int $index, bool $checked)` | Set item checked state |

## Read-only Properties

| Property | Type | Description |
|----------|------|-------------|
| `Count` | `int` | Number of items in the list |

## Example with Event Handler

```php
class MyPage extends Page
{
    public ?CheckListBox $CheckListBox1 = null;
    public ?Label $OutputLabel = null;

    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);
        $this->Name = 'MyPage';

        $this->CheckListBox1 = new CheckListBox($this);
        $this->CheckListBox1->Name = 'CheckListBox1';
        $this->CheckListBox1->Parent = $this;
        $this->CheckListBox1->Items = [
            'Email notifications',
            'SMS notifications',
            'Push notifications',
            'Weekly digest'
        ];
        $this->CheckListBox1->OnSubmit = 'CheckListBox1Submit';

        $this->OutputLabel = new Label($this);
        $this->OutputLabel->Name = 'OutputLabel';
        $this->OutputLabel->Parent = $this;
    }

    public function CheckListBox1Submit(object $sender, array $params): void
    {
        $checked = $this->CheckListBox1->Checked;
        $selected = [];

        foreach ($checked as $key => $value) {
            if ($value) {
                $selected[] = $this->CheckListBox1->Items[$key];
            }
        }

        $this->OutputLabel->Caption = 'Selected: ' . implode(', ', $selected);
    }
}
```

## Pre-selecting Items

```php
// Pre-select first and third items
$this->CheckListBox1->Checked = [0 => true, 2 => true];
```

## Multi-column Layout

```php
// Display items in 2 columns
$this->CheckListBox1->Columns = 2;
```

## Header Items

Header items are displayed without checkboxes and with distinct styling:

```php
$this->CheckListBox1->Items = ['Category A', 'Item 1', 'Item 2', 'Category B', 'Item 3'];
$this->CheckListBox1->Header = [0 => true, 3 => true];  // Mark items 0 and 3 as headers
```

## Notes

- The `Checked` property uses the same keys as the `Items` array
- Header items cannot be checked
- Use `selectAll()` to quickly check all items
- Form submission automatically updates the `Checked` property
