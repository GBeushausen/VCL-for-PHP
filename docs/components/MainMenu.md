# MainMenu

Encapsulates a menu bar and its accompanying drop-down menus.

**Namespace:** `VCL\Menus`
**File:** `src/VCL/Menus/MainMenu.php`
**Extends:** `CustomMainMenu`

## Usage

```php
use VCL\Menus\MainMenu;

$menu = new MainMenu($this);
$menu->Name = "MainMenu1";
$menu->Parent = $this;
$menu->Items = [
    ['Caption' => 'File', 'Tag' => 0, 'Items' => [
        ['Caption' => 'New', 'Tag' => 1],
        ['Caption' => 'Open', 'Tag' => 2],
        ['Caption' => '-'],  // Separator
        ['Caption' => 'Exit', 'Tag' => 3]
    ]],
    ['Caption' => 'Edit', 'Tag' => 0, 'Items' => [
        ['Caption' => 'Cut', 'Tag' => 10],
        ['Caption' => 'Copy', 'Tag' => 11],
        ['Caption' => 'Paste', 'Tag' => 12]
    ]]
];
$menu->OnClick = "MenuClick";
```

## Properties

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `Items` | `array` | `[]` | Menu item structure |
| `Images` | `mixed` | `null` | ImageList for menu icons |
| `Width` | `int` | `300` | Menu bar width |
| `Height` | `int` | `24` | Menu bar height |
| `Color` | `string` | `''` | Background color |
| `Visible` | `bool` | `true` | Show/hide menu |

## Item Properties

| Property | Type | Description |
|----------|------|-------------|
| `Caption` | `string` | Menu item text (use '-' for separator) |
| `Tag` | `int` | Identifier passed to click handler |
| `ImageIndex` | `int` | Index in Images list (-1 for none) |
| `Items` | `array` | Submenu items |

## Events

| Property | Type | Description |
|----------|------|-------------|
| `OnClick` | `?string` | Server-side click handler |
| `jsOnClick` | `?string` | JavaScript click handler |

## Example

```php
class MyPage extends Page
{
    public ?MainMenu $Menu = null;

    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);

        $this->Menu = new MainMenu($this);
        $this->Menu->Name = "Menu";
        $this->Menu->Parent = $this;
        $this->Menu->Items = [
            [
                'Caption' => 'File',
                'Tag' => 0,
                'Items' => [
                    ['Caption' => 'New Project', 'Tag' => 101],
                    ['Caption' => 'Open Project', 'Tag' => 102],
                    ['Caption' => 'Save', 'Tag' => 103],
                    ['Caption' => '-'],
                    ['Caption' => 'Exit', 'Tag' => 199]
                ]
            ],
            [
                'Caption' => 'Help',
                'Tag' => 0,
                'Items' => [
                    ['Caption' => 'Documentation', 'Tag' => 301],
                    ['Caption' => 'About', 'Tag' => 302]
                ]
            ]
        ];
        $this->Menu->OnClick = "MenuClick";
    }

    public function MenuClick(object $sender, array $params): void
    {
        $tag = $params['tag'] ?? 0;

        switch ($tag) {
            case 101:
                // New project
                break;
            case 199:
                // Exit
                break;
            case 302:
                // About dialog
                break;
        }
    }
}
```

## Generated HTML

```html
<nav id="Menu" class="vcl-mainmenu" style="width: 300px; height: 24px;">
    <ul class="vcl-menu-bar">
        <li class="vcl-menu-item has-submenu">
            <a href="#" data-tag="0" onclick="Menu_click(event, 0); return false;">
                <span>File</span>
                <span class="vcl-menu-arrow">▸</span>
            </a>
            <ul class="vcl-submenu">
                <li class="vcl-menu-item">
                    <a href="#" data-tag="101" onclick="Menu_click(event, 101);">New Project</a>
                </li>
                <!-- more items -->
            </ul>
        </li>
    </ul>
</nav>
<input type="hidden" id="Menu_state" name="Menu_state" value="" />
```

## Notes

- Uses CSS for dropdown display on hover
- Tag value identifies which item was clicked
- Use Tag=0 for parent items that only open submenus
- Separator items use Caption='-'
