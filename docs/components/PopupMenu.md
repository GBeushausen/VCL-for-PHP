# PopupMenu

Context menu that appears on right-click.

**Namespace:** `VCL\Menus`
**File:** `src/VCL/Menus/PopupMenu.php`
**Extends:** `CustomPopupMenu`

## Usage

```php
use VCL\Menus\PopupMenu;

$popup = new PopupMenu($this);
$popup->Name = "PopupMenu1";
$popup->Items = [
    ['Caption' => 'Cut', 'Tag' => 1],
    ['Caption' => 'Copy', 'Tag' => 2],
    ['Caption' => 'Paste', 'Tag' => 3],
    ['Caption' => '-'],
    ['Caption' => 'Delete', 'Tag' => 4]
];
$popup->OnClick = "PopupClick";
```

## Properties

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `Items` | `array` | `[]` | Menu item structure |
| `Images` | `mixed` | `null` | ImageList for menu icons |

## Item Properties

| Property | Type | Description |
|----------|------|-------------|
| `Caption` | `string` | Menu item text (use '-' for separator) |
| `Tag` | `int` | Identifier passed to click handler |
| `ImageIndex` | `int` | Index in Images list |

## Events

| Property | Type | Description |
|----------|------|-------------|
| `OnClick` | `?string` | Server-side click handler |
| `jsOnClick` | `?string` | JavaScript click handler |

## Attaching to Controls

Assign the PopupMenu to a control's `PopupMenu` property:

```php
$this->Grid1->PopupMenu = $this->PopupMenu1;
$this->Panel1->PopupMenu = $this->PopupMenu1;
```

## Example

```php
class MyPage extends Page
{
    public ?PopupMenu $ContextMenu = null;
    public ?Panel $Panel1 = null;

    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);

        // Create popup menu
        $this->ContextMenu = new PopupMenu($this);
        $this->ContextMenu->Name = "ContextMenu";
        $this->ContextMenu->Items = [
            ['Caption' => 'Edit', 'Tag' => 1],
            ['Caption' => 'Delete', 'Tag' => 2],
            ['Caption' => '-'],
            ['Caption' => 'Properties', 'Tag' => 3]
        ];
        $this->ContextMenu->OnClick = "ContextMenuClick";

        // Create panel with popup
        $this->Panel1 = new Panel($this);
        $this->Panel1->Name = "Panel1";
        $this->Panel1->Parent = $this;
        $this->Panel1->PopupMenu = $this->ContextMenu;
    }

    public function ContextMenuClick(object $sender, array $params): void
    {
        $tag = $params['tag'] ?? 0;

        switch ($tag) {
            case 1:
                // Edit action
                break;
            case 2:
                // Delete action
                break;
            case 3:
                // Properties action
                break;
        }
    }
}
```

## Notes

- Appears when user right-clicks on associated control
- Similar structure to MainMenu items
- Use Tag to identify clicked item
- Separator items use Caption='-'
