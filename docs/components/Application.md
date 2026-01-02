# Application

The root owner for all forms and pages in a VCL application.

**Namespace:** `VCL\Forms`
**File:** `src/VCL/Forms/Application.php`
**Extends:** `Component`

## Usage

```php
use VCL\Forms\Application;

$application = Application::getInstance();
$page = new MyPage($application);
$page->preinit();
$page->init();
$page->show();
```

## Properties

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `Language` | `string` | `''` | Application language code |

## Methods

| Method | Description |
|--------|-------------|
| `getInstance()` | Get singleton instance |
| `autoDetectLanguage()` | Detect language from browser |
| `shutdown()` | Serialize state to session |

## Singleton Pattern

Application uses the singleton pattern:

```php
// Get the application instance
$app = Application::getInstance();

// Same instance every time
$app2 = Application::getInstance();  // $app === $app2
```

## Session Management

Application automatically:
- Starts PHP session
- Stores GET parameters in session
- Serializes component state on shutdown

```php
// Clear session and restart
// Add ?restore_session to URL to clear all stored state
```

## Language Detection

```php
$app = Application::getInstance();
$app->autoDetectLanguage();

echo $app->Language;  // e.g., "de-DE" or "en-US"
```

## Example: Complete Page Setup

```php
<?php
declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use VCL\Forms\Application;
use VCL\Forms\Page;
use VCL\StdCtrls\Button;

class MyPage extends Page
{
    public ?Button $Button1 = null;

    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);
        $this->Name = "MyPage";

        $this->Button1 = new Button($this);
        $this->Button1->Name = "Button1";
        $this->Button1->Parent = $this;
        $this->Button1->Caption = "Click Me";
        $this->Button1->OnClick = "Button1Click";
    }

    public function Button1Click(object $sender, array $params): void
    {
        // Handle click
    }
}

// Application entry point
$application = Application::getInstance();
$page = new MyPage($application);
$page->preinit();  // Read form values
$page->init();     // Process events
$page->show();     // Render HTML
```

## Notes

- Always use `getInstance()` to get the Application
- Application handles session lifecycle
- Language can be auto-detected from browser Accept-Language header
- State is automatically persisted between requests
