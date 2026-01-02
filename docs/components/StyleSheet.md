# StyleSheet

Allows importing and using CSS stylesheets.

**Namespace:** `VCL\Styles`
**File:** `src/VCL/Styles/StyleSheet.php`
**Extends:** `CustomStyleSheet`

## Usage

```php
use VCL\Styles\StyleSheet;

$styles = new StyleSheet($this);
$styles->Name = "StyleSheet1";
$styles->FileName = "css/styles.css";
```

## Properties

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `FileName` | `string` | `''` | Path to CSS file |
| `IncludeStandard` | `bool` | `true` | Include standard CSS classes |
| `IncludeID` | `bool` | `true` | Include ID selectors |
| `IncludeSubStyle` | `bool` | `true` | Include nested styles |

## Purpose

StyleSheet component:
1. Links a CSS file to your page
2. Parses CSS classes for use in component Style properties
3. Makes styles available in the IDE designer

## Example

```php
class MyPage extends Page
{
    public ?StyleSheet $Styles = null;
    public ?Button $Button1 = null;

    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);

        // Load stylesheet
        $this->Styles = new StyleSheet($this);
        $this->Styles->Name = "Styles";
        $this->Styles->FileName = "css/theme.css";

        // Apply style to control
        $this->Button1 = new Button($this);
        $this->Button1->Name = "Button1";
        $this->Button1->Parent = $this;
        $this->Button1->Caption = "Styled Button";
        $this->Button1->Style = "btn-primary";  // Class from theme.css
    }
}
```

## CSS File Example

```css
/* css/theme.css */
.btn-primary {
    background-color: #007bff;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 4px;
}

.btn-primary:hover {
    background-color: #0056b3;
}

.panel-header {
    background-color: #f5f5f5;
    border-bottom: 1px solid #ddd;
    padding: 10px;
    font-weight: bold;
}

.form-input {
    width: 100%;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 4px;
}
```

## Using Styles on Controls

Most controls have a `Style` property:

```php
$this->Panel1->Style = "panel-header";
$this->Edit1->Style = "form-input";
$this->Button1->Style = "btn-primary";
```

## Generated HTML

```html
<link rel="stylesheet" href="css/theme.css" type="text/css" />
```

## Notes

- CSS file is linked in page header
- Style property applies CSS class to control
- Multiple StyleSheet components can be used
- IDE designer shows available styles from parsed CSS
