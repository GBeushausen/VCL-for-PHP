# Page

Main page component representing a complete HTML page with support for Tailwind CSS and htmx.

**Namespace:** `VCL\Forms`
**File:** `src/VCL/Forms/Page.php`
**Extends:** `CustomPage`

## Overview

The Page component is the main container for VCL applications. It generates a complete HTML document including DOCTYPE, head, and body sections. It provides built-in support for:

- **Tailwind CSS 4** - Theme-aware styling with light/dark mode
- **htmx** - Declarative AJAX for interactive components
- **Forms** - Automatic form wrapping with configurable encoding
- **Responsive design** - Viewport meta tag for mobile support

## Basic Usage

```php
use VCL\Forms\Page;
use VCL\Forms\Application;

class MyPage extends Page
{
    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);
        $this->Name = 'MyPage';
        $this->Caption = 'Page Title';
    }
}

$application = Application::getInstance();
$page = new MyPage($application);
$page->show();
```

## Tailwind CSS Integration

Enable Tailwind CSS for modern, theme-aware styling:

```php
class TailwindPage extends Page
{
    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);
        $this->Name = 'TailwindPage';
        $this->Caption = 'My Tailwind Page';

        // Enable Tailwind CSS
        $this->UseTailwind = true;
        $this->TailwindStylesheet = '/assets/css/vcl-theme.css';
        $this->BodyClasses = ['bg-vcl-surface-sunken', 'min-h-screen', 'p-8'];
        $this->DefaultTheme = 'light'; // or 'dark'
    }

    public function dumpChildren(): void
    {
        // Your page content with Tailwind classes
        echo '<div class="max-w-4xl mx-auto">';
        echo '<h1 class="text-3xl font-bold text-vcl-text">Hello World</h1>';
        echo '</div>';
    }
}
```

### Theme Switching

When Tailwind is enabled, a `VCLTheme` JavaScript object is automatically included:

```javascript
// Toggle between light and dark mode
VCLTheme.toggle();

// Get current theme
VCLTheme.get(); // Returns 'light' or 'dark'

// Set specific theme
VCLTheme.set('dark');
```

Use it in a button:

```php
$btn = new Button();
$btn->Caption = 'Toggle Dark Mode';
$btn->ExtraAttributes = 'onclick="VCLTheme?.toggle()"';
```

### Generated HTML with Tailwind

```html
<!DOCTYPE html>
<html lang="en" dir="ltr" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Tailwind Page</title>
    <link rel="stylesheet" href="/assets/css/vcl-theme.css">
</head>
<body class="bg-vcl-surface-sunken min-h-screen p-8">
    <!-- Page content -->
    <script>
    (function() {
        const VCLTheme = { /* theme switching logic */ };
        VCLTheme.init();
        window.VCLTheme = VCLTheme;
    })();
    </script>
</body>
</html>
```

## htmx Integration

Enable htmx for declarative AJAX functionality:

```php
class InteractivePage extends Page
{
    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);
        $this->Name = 'InteractivePage';
        $this->Caption = 'Interactive Page';

        // Enable htmx
        $this->UseHtmx = true;
        $this->UseHtmxDebug = true; // Optional: enable console logging
    }

    /**
     * Handle htmx AJAX requests.
     */
    public function processHtmx(): void
    {
        if (!\VCL\Ajax\HtmxHandler::isHtmxRequest()) {
            return;
        }

        // Return HTML fragment for AJAX response
        header('Content-Type: text/html; charset=UTF-8');
        echo '<div class="result">Request processed!</div>';
        exit;
    }
}
```

### htmx with Form Inputs

```php
$edit = new Edit();
$edit->Name = 'SearchInput';
$edit->RenderMode = RenderMode::Tailwind;
$edit->Placeholder = 'Type to search...';
$edit->ExtraAttributes = 'hx-post="" hx-trigger="keyup changed delay:300ms" hx-target="#results"';
```

## Properties

### Document Properties

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `Caption` | `string` | `''` | Page title (HTML `<title>`) |
| `DocType` | `DocType` | `DocType::HTML5` | Document type declaration |
| `Encoding` | `string` | `'UTF-8\|utf-8'` | Character encoding |
| `Language` | `string` | `'(default)'` | Page language (de, en, etc.) |
| `Directionality` | `Directionality` | `LeftToRight` | Text direction (ltr/rtl) |
| `Icon` | `string` | `''` | Favicon path |

### Layout Properties

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `Color` | `string` | `''` | Background color |
| `Background` | `string` | `''` | Background image path |
| `LeftMargin` | `int` | `0` | Left margin in pixels |
| `TopMargin` | `int` | `0` | Top margin in pixels |
| `RightMargin` | `int` | `0` | Right margin in pixels |
| `BottomMargin` | `int` | `0` | Bottom margin in pixels |

### Form Properties

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `IsForm` | `bool` | `true` | Wrap content in `<form>` tag |
| `Action` | `string` | `''` | Form action URL |
| `FormEncoding` | `string` | `''` | Form encoding type (e.g., `multipart/form-data`) |
| `ShowHeader` | `bool` | `true` | Output HTML header |
| `ShowFooter` | `bool` | `true` | Output HTML footer |

### Tailwind CSS Properties

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `UseTailwind` | `bool` | `false` | Enable Tailwind CSS mode |
| `TailwindStylesheet` | `string` | `''` | Path to compiled Tailwind CSS file |
| `BodyClasses` | `array` | `[]` | CSS classes for the body tag |
| `DefaultTheme` | `string` | `'light'` | Initial theme ('light' or 'dark') |

### htmx Properties

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `UseHtmx` | `bool` | `false` | Include htmx library |
| `UseHtmxDebug` | `bool` | `false` | Enable htmx console logging |

### JavaScript Events

| Property | Type | Description |
|----------|------|-------------|
| `jsOnLoad` | `?string` | JavaScript function name for page load |
| `jsOnUnload` | `?string` | JavaScript function name for page unload |

### PHP Events

| Property | Type | Description |
|----------|------|-------------|
| `OnBeforeShowHeader` | `?string` | Called before header is rendered |
| `OnShowHeader` | `?string` | Called during header rendering |
| `OnStartBody` | `?string` | Called after body tag opens |
| `OnAfterShowFooter` | `?string` | Called after footer is rendered |
| `OnCreate` | `?string` | Called when page is created |
| `OnBeforeAjaxProcess` | `?string` | Called before htmx request processing |

## Methods

### Lifecycle Methods

| Method | Description |
|--------|-------------|
| `preinit()` | Reads submitted form values into component properties |
| `init()` | Processes events, calls event handlers |
| `show()` | Renders and outputs HTML |

### Rendering Methods

| Method | Description |
|--------|-------------|
| `dumpHeader()` | Outputs DOCTYPE, html, and head tags |
| `dumpBodyStart()` | Outputs opening body tag with classes/styles |
| `dumpChildren()` | Override this to render page content |
| `dumpContents()` | Main rendering method (calls other dump methods) |

### Utility Methods

| Method | Description |
|--------|-------------|
| `getCharset()` | Returns the charset from encoding property |
| `getLanguageCode()` | Returns the language code for HTML lang attribute |
| `readStartForm()` | Returns the opening form tag HTML |
| `readEndForm()` | Returns the closing form tag HTML |
| `processHtmx()` | Override to handle htmx AJAX requests |

## Complete Example

A full example combining Tailwind CSS, htmx, and VCL components:

```php
<?php
declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use VCL\Forms\Application;
use VCL\Forms\Page;
use VCL\ExtCtrls\FlexPanel;
use VCL\StdCtrls\Button;
use VCL\StdCtrls\Edit;
use VCL\StdCtrls\Label;
use VCL\UI\Enums\FlexDirection;
use VCL\UI\Enums\RenderMode;

class ContactPage extends Page
{
    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);

        $this->Name = 'ContactPage';
        $this->Caption = 'Contact Form';
        $this->Language = 'en';

        // Enable Tailwind CSS
        $this->UseTailwind = true;
        $this->TailwindStylesheet = __DIR__ . '/public/assets/css/vcl-theme.css';
        $this->BodyClasses = ['bg-vcl-surface-sunken', 'min-h-screen', 'p-8'];

        // Enable htmx
        $this->UseHtmx = true;

        // Disable form wrapper (we'll handle form submission via htmx)
        $this->IsForm = false;
    }

    /**
     * Handle htmx AJAX requests.
     */
    public function processHtmx(): void
    {
        if (!\VCL\Ajax\HtmxHandler::isHtmxRequest()) {
            return;
        }

        header('Content-Type: text/html; charset=UTF-8');

        $name = htmlspecialchars($_POST['NameEdit'] ?? '');
        echo "<div class='text-vcl-text'>Hello, {$name}!</div>";
        exit;
    }

    /**
     * Render page content.
     */
    public function dumpChildren(): void
    {
        echo '<div class="max-w-md mx-auto">';

        // Create a form panel
        $form = new FlexPanel();
        $form->Name = 'ContactForm';
        $form->Direction = FlexDirection::Column;
        $form->FlexGap = 'gap-4';
        $form->Padding = 'p-6';
        $form->Classes = ['bg-vcl-surface-elevated', 'rounded-lg', 'shadow-vcl-md'];

        // Name label
        $label = new Label($form);
        $label->Name = 'NameLabel';
        $label->Caption = 'Your Name';
        $label->RenderMode = RenderMode::Tailwind;
        $label->Parent = $form;

        // Name input with htmx
        $edit = new Edit($form);
        $edit->Name = 'NameEdit';
        $edit->RenderMode = RenderMode::Tailwind;
        $edit->Placeholder = 'Enter your name';
        $edit->ExtraAttributes = 'hx-post="" hx-trigger="keyup changed delay:300ms" hx-target="#greeting"';
        $edit->Parent = $form;

        // Submit button
        $btn = new Button($form);
        $btn->Name = 'SubmitBtn';
        $btn->Caption = 'Submit';
        $btn->RenderMode = RenderMode::Tailwind;
        $btn->ThemeVariant = 'primary';
        $btn->Parent = $form;

        $form->dumpContents();

        // Greeting output area
        echo '<div id="greeting" class="mt-4 p-4 bg-vcl-surface-elevated rounded-lg"></div>';

        echo '</div>';
    }
}

// Run the application
$app = Application::getInstance();
$page = new ContactPage($app);
$page->show();
```

## See Also

- [Application](Application.md) - Application singleton
- [FlexPanel](../layout/FlexPanel.md) - Flexible box layout panel
- [GridPanel](../layout/GridPanel.md) - CSS Grid layout panel
- [Button](Button.md) - Button component
- [Edit](Edit.md) - Text input component
- [demo_tailwind.php](../../examples/demo_tailwind.php) - Full working demo
