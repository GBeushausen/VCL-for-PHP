# ActionList

A component for URL-based action routing.

**Namespace:** `VCL\Actions`
**File:** `src/VCL/Actions/ActionList.php`
**Extends:** `Component`

## Usage

```php
use VCL\Actions\ActionList;

$actionList = new ActionList($this);
$actionList->Name = 'ActionList1';
$actionList->Actions = ['view', 'edit', 'delete', 'export'];
$actionList->OnExecute = 'ActionList1Execute';
```

## How It Works

ActionList monitors URL parameters for action requests. When a request contains a parameter matching the component name with a valid action value, the `OnExecute` event fires.

**URL Format:** `http://yoursite.com/page.php?ActionList1=view`

## Properties

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `Actions` | `array` | `[]` | Array of valid action names |
| `OnExecute` | `string\|null` | `null` | Event handler for action execution |

## Methods

| Method | Description |
|--------|-------------|
| `addAction(string $action)` | Add an action to the list |
| `deleteAction(string $action)` | Remove an action from the list |
| `hasAction(string $action)` | Check if action exists |
| `executeAction(string $action)` | Manually trigger an action |
| `expandActionToURL(string $action, string &$url)` | Add action parameter to URL |
| `getActionURL(string $action, string $baseUrl = '')` | Get complete URL for an action |

## Example with Event Handler

```php
class MyPage extends Page
{
    public ?ActionList $ActionList1 = null;
    public ?Label $MessageLabel = null;

    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);
        $this->Name = 'MyPage';

        $this->ActionList1 = new ActionList($this);
        $this->ActionList1->Name = 'ActionList1';
        $this->ActionList1->Actions = ['show', 'hide', 'refresh'];
        $this->ActionList1->OnExecute = 'ActionList1Execute';

        $this->MessageLabel = new Label($this);
        $this->MessageLabel->Name = 'MessageLabel';
        $this->MessageLabel->Parent = $this;
    }

    public function ActionList1Execute(object $sender, array $params): void
    {
        match ($params['action']) {
            'show' => $this->MessageLabel->Caption = 'Message is now visible',
            'hide' => $this->MessageLabel->Caption = '',
            'refresh' => $this->MessageLabel->Caption = 'Data refreshed at ' . date('H:i:s'),
            default => null,
        };
    }
}
```

## Generating Action Links

```php
// Method 1: Using expandActionToURL
$url = '/mypage.php';
$this->ActionList1->expandActionToURL('edit', $url);
// Result: /mypage.php?ActionList1=edit

// Method 2: Using getActionURL
$url = $this->ActionList1->getActionURL('delete');
// Result: /current-script.php?ActionList1=delete

// Method 3: Using getActionURL with custom base
$url = $this->ActionList1->getActionURL('view', '/admin/items.php');
// Result: /admin/items.php?ActionList1=view
```

## HTML Link Example

```php
public function dumpContents(): void
{
    $viewUrl = $this->ActionList1->getActionURL('view');
    $editUrl = $this->ActionList1->getActionURL('edit');
    $deleteUrl = $this->ActionList1->getActionURL('delete');

    echo '<nav>';
    echo '<a href="' . htmlspecialchars($viewUrl) . '">View</a> | ';
    echo '<a href="' . htmlspecialchars($editUrl) . '">Edit</a> | ';
    echo '<a href="' . htmlspecialchars($deleteUrl) . '">Delete</a>';
    echo '</nav>';
}
```

## Programmatic Action Execution

```php
// Manually trigger an action (e.g., from another event handler)
$this->ActionList1->executeAction('refresh');
```

## Dynamic Actions

```php
// Add actions at runtime
$this->ActionList1->addAction('archive');
$this->ActionList1->addAction('duplicate');

// Remove an action
$this->ActionList1->deleteAction('delete');

// Check if action exists
if ($this->ActionList1->hasAction('edit')) {
    // Show edit button
}
```

## Notes

- Only actions in the `Actions` array will trigger the `OnExecute` event
- Actions are case-sensitive
- Multiple ActionList components can coexist on the same page
- Use `hasAction()` before generating links to validate action availability
