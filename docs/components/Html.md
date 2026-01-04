# Html

Component for rendering raw HTML content or Twig templates.

**Namespace:** `VCL\StdCtrls`
**File:** `src/VCL/StdCtrls/Html.php`
**Extends:** `GraphicControl`

## Overview

The Html component provides a clean way to render HTML content without using `ob_start()`/`ob_get_clean()` patterns. It supports:

- **Direct HTML** - Set HTML content via the `Html` property
- **Twig Templates** - Render Twig templates with variables
- **Tailwind CSS** - Full support for Tailwind render mode
- **Wrapper Control** - Optional wrapper tag with CSS classes

## Installation

For Twig template support, install Twig:

```bash
composer require twig/twig
```

## Basic Usage

### Direct HTML Content

```php
use VCL\StdCtrls\Html;

$content = new Html();
$content->Name = 'MyContent';
$content->Html = '<h1>Hello World</h1><p>Welcome to VCL!</p>';
$content->dumpContents();
```

Output:
```html
<div id="MyContent"><h1>Hello World</h1><p>Welcome to VCL!</p></div>
```

### Without Wrapper

```php
$content = new Html();
$content->UseWrapper = false;
$content->Html = '<h1>Hello World</h1>';
$content->dumpContents();
```

Output:
```html
<h1>Hello World</h1>
```

### Custom Wrapper Tag

```php
$content = new Html();
$content->WrapperTag = 'section';
$content->Html = '<p>Content here</p>';
$content->dumpContents();
```

Output:
```html
<section id="..."><p>Content here</p></section>
```

## Twig Template Support

### Basic Template Rendering

```php
use VCL\StdCtrls\Html;

$card = new Html();
$card->Name = 'UserCard';
$card->TemplatePath = __DIR__ . '/templates';
$card->Template = 'card.twig';
$card->Variables = [
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'avatar' => '/images/john.jpg',
];
$card->dumpContents();
```

**templates/card.twig:**
```twig
<div class="bg-white rounded-lg shadow p-4">
    <img src="{{ avatar }}" alt="{{ name }}" class="w-16 h-16 rounded-full">
    <h3 class="font-bold text-lg">{{ name }}</h3>
    <p class="text-gray-600">{{ email }}</p>
</div>
```

### Setting Variables Fluently

```php
$card = new Html();
$card->TemplatePath = __DIR__ . '/templates';
$card->Template = 'card.twig';
$card->setVariable('name', 'John Doe')
     ->setVariable('email', 'john@example.com')
     ->addVariables([
         'role' => 'Administrator',
         'status' => 'active',
     ]);
```

### VCL Twig Functions and Filters

The Html component registers VCL-specific Twig extensions:

**Functions:**
```twig
{{ vcl_escape_html(text) }}
{{ vcl_escape_attr(value) }}
{{ vcl_escape_js(data) }}
{{ vcl_escape_url(url) }}
```

**Filters:**
```twig
{{ text|vcl_html }}
{{ value|vcl_attr }}
```

## Tailwind CSS Mode

```php
use VCL\StdCtrls\Html;
use VCL\UI\Enums\RenderMode;

$content = new Html();
$content->Name = 'AlertBox';
$content->RenderMode = RenderMode::Tailwind;
$content->Classes = ['bg-vcl-surface-elevated', 'rounded-lg', 'p-4', 'shadow-vcl-md'];
$content->Html = '<p class="text-vcl-text">This is an alert message.</p>';
$content->dumpContents();
```

Output:
```html
<div id="AlertBox" class="bg-vcl-surface-elevated rounded-lg p-4 shadow-vcl-md">
    <p class="text-vcl-text">This is an alert message.</p>
</div>
```

## Properties

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `Html` | `string` | `''` | Direct HTML content to render |
| `Template` | `string` | `''` | Twig template filename (e.g., `card.twig`) |
| `TemplatePath` | `string` | `''` | Base path for Twig templates |
| `Variables` | `array` | `[]` | Variables passed to Twig template |
| `EscapeHtml` | `bool` | `false` | Escape HTML content (for user input) |
| `WrapperTag` | `string` | `'div'` | HTML tag for wrapper element |
| `UseWrapper` | `bool` | `true` | Whether to wrap content in a tag |

### Inherited Properties

| Property | Type | Description |
|----------|------|-------------|
| `Name` | `string` | Component name (used as HTML id) |
| `RenderMode` | `RenderMode` | Classic or Tailwind rendering |
| `Classes` | `array` | CSS classes for Tailwind mode |
| `Hidden` | `bool` | Hide the component |
| `Width` | `int` | Width in pixels |
| `Height` | `int` | Height in pixels |

## Methods

### Content Methods

| Method | Returns | Description |
|--------|---------|-------------|
| `getContent()` | `string` | Get rendered content (HTML or template) |
| `renderContent()` | `string` | Render content without wrapper |
| `setVariable(name, value)` | `self` | Set a single template variable (fluent) |
| `addVariables(array)` | `self` | Add multiple template variables (fluent) |

### Rendering Methods

| Method | Description |
|--------|-------------|
| `dumpContents()` | Output the component HTML |
| `render()` | Get the component HTML as string |

### Static Methods

| Method | Description |
|--------|-------------|
| `setTwigEnvironment(env)` | Set a custom Twig environment |
| `configureTwig(options)` | Reset Twig with new options |

## Advanced Usage

### Custom Twig Environment

```php
use VCL\StdCtrls\Html;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

// Create custom Twig environment
$loader = new FilesystemLoader([
    __DIR__ . '/templates',
    __DIR__ . '/shared-templates',
]);

$twig = new Environment($loader, [
    'cache' => __DIR__ . '/cache/twig',
    'auto_reload' => true,
]);

// Add custom extensions
$twig->addGlobal('app_name', 'My Application');

// Use with Html component
Html::setTwigEnvironment($twig);

$content = new Html();
$content->Template = 'page.twig';
$content->dumpContents();
```

### Escaped User Content

```php
// For user-provided content, enable escaping
$content = new Html();
$content->EscapeHtml = true;
$content->Html = $userInput; // Will be escaped
$content->dumpContents();
```

### In Page Component

```php
class MyPage extends Page
{
    public function dumpChildren(): void
    {
        // Header section
        $header = new Html();
        $header->Classes = ['text-3xl', 'font-bold', 'mb-4'];
        $header->Html = 'Welcome to My App';
        $header->dumpContents();

        // Card from template
        $card = new Html();
        $card->TemplatePath = __DIR__ . '/templates';
        $card->Template = 'user-card.twig';
        $card->Variables = $this->getUserData();
        $card->RenderMode = RenderMode::Tailwind;
        $card->Classes = ['max-w-md', 'mx-auto'];
        $card->dumpContents();
    }
}
```

## Complete Example

**page.php:**
```php
<?php
declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use VCL\Forms\Application;
use VCL\Forms\Page;
use VCL\StdCtrls\Html;
use VCL\UI\Enums\RenderMode;

class DemoPage extends Page
{
    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);
        $this->Name = 'DemoPage';
        $this->Caption = 'Html Component Demo';
        $this->UseTailwind = true;
        $this->TailwindStylesheet = '/assets/css/vcl-theme.css';
        $this->BodyClasses = ['bg-vcl-surface-sunken', 'min-h-screen', 'p-8'];
    }

    public function dumpChildren(): void
    {
        echo '<div class="max-w-4xl mx-auto">';

        // Page title using Html component
        $title = new Html();
        $title->Name = 'PageTitle';
        $title->WrapperTag = 'h1';
        $title->RenderMode = RenderMode::Tailwind;
        $title->Classes = ['text-3xl', 'font-bold', 'text-vcl-text', 'mb-8'];
        $title->Html = 'User Dashboard';
        $title->dumpContents();

        // User cards from Twig template
        $users = [
            ['name' => 'Alice', 'email' => 'alice@example.com', 'role' => 'Admin'],
            ['name' => 'Bob', 'email' => 'bob@example.com', 'role' => 'User'],
            ['name' => 'Charlie', 'email' => 'charlie@example.com', 'role' => 'Editor'],
        ];

        echo '<div class="grid grid-cols-3 gap-4">';
        foreach ($users as $i => $user) {
            $card = new Html();
            $card->Name = "UserCard{$i}";
            $card->TemplatePath = __DIR__ . '/templates';
            $card->Template = 'user-card.twig';
            $card->Variables = $user;
            $card->RenderMode = RenderMode::Tailwind;
            $card->Classes = ['bg-vcl-surface-elevated', 'rounded-lg', 'shadow-vcl-md'];
            $card->dumpContents();
        }
        echo '</div>';

        echo '</div>';
    }
}

$app = Application::getInstance();
$page = new DemoPage($app);
$page->show();
```

**templates/user-card.twig:**
```twig
<div class="p-4">
    <div class="flex items-center gap-3 mb-3">
        <div class="w-10 h-10 bg-vcl-primary rounded-full flex items-center justify-center text-white font-bold">
            {{ name|first|upper }}
        </div>
        <div>
            <h3 class="font-semibold text-vcl-text">{{ name }}</h3>
            <p class="text-sm text-vcl-text-muted">{{ email }}</p>
        </div>
    </div>
    <span class="inline-block px-2 py-1 text-xs rounded bg-vcl-surface-sunken text-vcl-text-muted">
        {{ role }}
    </span>
</div>
```

## See Also

- [Label](Label.md) - Simple text display
- [Page](Page.md) - Page component with Tailwind support
- [FlexPanel](../layout/FlexPanel.md) - Flexible layout container
