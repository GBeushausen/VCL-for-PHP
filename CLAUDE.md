# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

VCL for PHP 3.0 is a Delphi-inspired Visual Component Library framework, originally developed by qadram software S.L. (2004-2008). It enables building web applications using a component-based paradigm similar to Delphi's VCL.

**Branch `php84-migration`**: Fully modernized version for **PHP 8.4+ only**, featuring:
- Composer with PSR-4 autoloading
- PHP 8.4 Property Hooks (native getters/setters)
- Enums for type-safe constants
- Full backwards compatibility with legacy code

## Development Environment

### Using DDEV (Recommended)
```bash
ddev start              # Start containers (PHP 8.4)
ddev ssh                # Shell into web container
ddev xdebug on          # Enable debugging
ddev stop               # Stop containers
```
Access at: `http://vcl.ddev.site`

### Composer
```bash
composer install        # Install dependencies
composer dump-autoload  # Regenerate autoloader
composer test           # Run PHPUnit tests
composer analyse        # Run PHPStan
```

## Project Structure

### Modern Structure (src/)
```
src/VCL/
├── Core/
│   ├── VCLObject.php              # Base class for all objects
│   ├── Component.php              # Component with Property Hooks
│   ├── Input.php                  # Type-safe request handling
│   ├── InputParam.php             # Parameter with sanitization
│   ├── InputSource.php            # Enum: GET/POST/COOKIES/SERVER
│   ├── LegacyConstants.php        # sGET, sPOST etc. for compatibility
│   ├── LegacyAliases.php          # class_alias() mappings
│   └── Exception/
│       └── PropertyNotFoundException.php
└── UI/
    ├── Control.php                # Visual control base (Property Hooks)
    ├── StdCtrls/
    │   └── Button.php             # Example control
    └── Enums/
        ├── Alignment.php          # alNone, alTop, alClient...
        ├── Anchors.php            # agLeft, agCenter, agRight
        ├── BorderStyle.php        # bsNone, bsSingle, bsRaised...
        ├── ButtonType.php         # btSubmit, btReset, btNormal
        ├── CharCase.php           # ecNormal, ecUpperCase...
        ├── Cursor.php             # crPointer, crWait...
        └── LegacyConstants.php    # Legacy constant definitions
```

### Legacy Structure (root)
Original `.inc.php` files remain for backwards compatibility:
- `vcl.inc.php`, `system.inc.php` → Now wrappers loading modern classes
- `classes.inc.php`, `controls.inc.php`, `forms.inc.php` → Original code
- `legacy/` → Backup of original wrapper files

## Usage

### Modern Way (Recommended)
```php
<?php
require_once 'vendor/autoload.php';

use VCL\Core\Component;
use VCL\UI\Control;
use VCL\UI\StdCtrls\Button;
use VCL\UI\Enums\Alignment;

$btn = new Button();
$btn->Name = 'Button1';
$btn->Caption = 'Click Me';
$btn->Left = 50;
$btn->Top = 100;
$btn->Align = Alignment::Top;
$btn->OnClick = fn($sender) => print("Clicked!");

echo $btn->render();
```

### Legacy Way (Still Supported)
```php
<?php
require_once("vcl.inc.php");
use_unit("forms.inc.php");
use_unit("stdctrls.inc.php");

// Old code continues to work
$btn = new Button($this);
$btn->Caption = "Click Me";
```

## PHP 8.4 Features

### Property Hooks (Replaces __get/__set)
```php
class Control extends Component
{
    private string $_caption = '';

    // Native getter/setter syntax
    public string $Caption {
        get => $this->_caption;
        set => $this->_caption = $value;
    }

    // Computed/read-only property
    public int $Right {
        get => $this->_left + ($this->_width ?? 0);
    }

    // Setter with validation
    public int $Width {
        get => $this->_width;
        set {
            if ($value < 0) $value = 0;
            $this->_width = $value;
        }
    }
}
```

### Enums (Replaces Constants)
```php
// Old way
define('alTop', 'alTop');
$ctrl->Align = alTop;

// New way
use VCL\UI\Enums\Alignment;
$ctrl->Align = Alignment::Top;

// Enums have methods
echo Alignment::Top->toCss();  // "absolute; top: 0; left: 0; right: 0"
```

### Available Enums
| Enum | Values | Methods |
|------|--------|---------|
| `Alignment` | None, Top, Bottom, Left, Right, Client, Custom | `toCss()` |
| `Anchors` | None, Left, Center, Right | `toCss()` |
| `BorderStyle` | None, Single, Box, Frame, Lowered, Raised... | `toCss()` |
| `ButtonType` | Submit, Reset, Normal | `toHtml()` |
| `CharCase` | Normal, LowerCase, UpperCase | `toCss()`, `transform()` |
| `Cursor` | Pointer, CrossHair, Wait, Help... | `toCss()` |
| `InputSource` | GET, POST, REQUEST, COOKIES, SERVER | `getArray()` |

## Architecture

### Modern Class Hierarchy
```
VCL\Core\VCLObject
  └── VCL\Core\Component (Property Hooks)
      └── VCL\UI\Control (Property Hooks)
          └── VCL\UI\StdCtrls\Button
```

### Legacy Class Hierarchy
```
VCLObject (system.inc.php)
  └── Persistent (classes.inc.php)
      └── Component (classes.inc.php)
          ├── Control (controls.inc.php)
          │   └── FocusControl → Page, DataModule
          ├── CustomConnection (db.inc.php)
          │   └── MySQLDatabase, OracleDatabase
          └── DataSet (db.inc.php)
```

## Key Patterns

### Property Access
```php
// Modern (Property Hooks) - IDE autocomplete works!
$ctrl->Caption = "Text";
$ctrl->Align = Alignment::Top;

// Legacy (Magic Methods) - still works
$ctrl->Caption = "Text";  // Calls __set → setCaption()
```

### Component Creation
```php
// Modern
$btn = new Button();
$btn->Name = 'Button1';
$btn->Parent = $panel;

// Legacy (with preinit/init cycle)
$page = new MyPage($application);
$page->preinit();
$page->init();
$page->show();
```

### Input Handling
```php
// Modern
use VCL\Core\Input;
$input = new Input();
$name = $input->username?->asString() ?? 'Guest';
$id = $input->post('id')?->asInteger() ?? 0;

// Legacy
global $input;
$action = $input->action;
if (is_object($action)) {
    $value = $action->asString();
}
```

## Running Tests

```bash
# PHPUnit tests
composer test

# Or manually
cd tests
php run_php_tests.bat
```

Test configuration:
- `tests/mysqlcfg.inc.php` - MySQL settings
- `tests/interbasecfg.inc.php` - InterBase settings

## Migration from Legacy

### Step 1: Use Composer Autoloading
```php
// Instead of
require_once("vcl.inc.php");
use_unit("forms.inc.php");

// Use
require_once 'vendor/autoload.php';
use VCL\UI\Control;
```

### Step 2: Use Enums
```php
// Instead of
$ctrl->Align = alTop;
$ctrl->Cursor = crPointer;

// Use
use VCL\UI\Enums\{Alignment, Cursor};
$ctrl->Align = Alignment::Top;
$ctrl->Cursor = Cursor::Pointer;
```

### Step 3: Use New Classes
```php
// Instead of legacy Control from controls.inc.php
// Use VCL\UI\Control with Property Hooks
use VCL\UI\StdCtrls\Button;

$btn = new Button();
$btn->Caption = 'Click';  // Property Hook, not magic method
```

## Legacy Compatibility

All legacy code continues to work:
- `use_unit()` loads modern classes when available, falls back to legacy
- Old constant names (`alTop`, `sGET`, etc.) are still defined
- Class aliases map old names to new namespaced classes
- Magic methods (`__get`/`__set`) still work in `VCLObject`

## PHP 8.4 Migration Notes

### Completed Migrations
| Original | Replacement | Reason |
|----------|-------------|--------|
| `class Object` | `class VCLObject` | "Object" reserved in PHP 7.2+ |
| `each($array)` | `foreach` | Removed in PHP 8.0 |
| `$str{0}` | `$str[0]` | Curly brace syntax removed |
| `mysql_*` | `mysqli_*` | mysql extension removed |
| `split()` | `preg_split()`/`explode()` | Removed in PHP 7.0 |
| `utf8_decode()` | `mb_convert_encoding()` | Deprecated in PHP 8.2 |
| Magic `__get/__set` | Property Hooks | PHP 8.4 native syntax |
| `define()` constants | Enums | Type-safe, with methods |

### JavaScript Fixes
- Removed `var event = event || window.event`
- Removed `document.all` and `document.layers`
- Use `document.getElementById()` instead
