# Page

Main page component representing a complete HTML page.

**Namespace:** `VCL\Forms`
**File:** `src/VCL/Forms/Page.php`
**Extends:** `CustomPage`

## Usage

```php
use VCL\Forms\Page;
use VCL\Forms\Application;

class MyPage extends Page
{
    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);
        $this->Name = "MyPage";
        $this->Caption = "Page Title";
        $this->Color = "#ffffff";
    }
}

$application = Application::getInstance();
$page = new MyPage($application);
$page->preinit();
$page->init();
$page->show();
```

## Properties

### Document Properties

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `Caption` | `string` | `''` | Page title (HTML `<title>`) |
| `DocType` | `DocType` | `DocType::HTML5` | Document type |
| `Encoding` | `string` | `'UTF-8\|utf-8'` | Character encoding |
| `Language` | `string` | `'(default)'` | Page language |
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
| `FormEncoding` | `string` | `''` | Form encoding type |
| `ShowHeader` | `bool` | `true` | Output HTML header |
| `ShowFooter` | `bool` | `true` | Output HTML footer |

### JavaScript Events

| Property | Type | Description |
|----------|------|-------------|
| `jsOnLoad` | `?string` | JavaScript function name for page load |
| `jsOnUnload` | `?string` | JavaScript function name for page unload |

## Methods

### Lifecycle Methods

| Method | Description |
|--------|-------------|
| `preinit()` | Reads submitted form values into component properties |
| `init()` | Processes events, calls event handlers |
| `show()` | Renders and outputs HTML |

## Generated HTML

```html
<!DOCTYPE html>
<html lang="de" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Page Title</title>
</head>
<body style="background-color: #ffffff">
    <form id="MyPage_form" name="MyPage_form" method="post" action="">
        <div class="vcl-container" style="position: relative; width: 100%; min-height: 100vh">
            <!-- Child components rendered here -->
        </div>
    </form>
</body>
</html>
```

## Example

See [demo_simple.php](../../demo_simple.php) for a complete working example.
