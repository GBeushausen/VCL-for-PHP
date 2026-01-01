# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

VCL for PHP 2.0 is a Delphi-inspired Visual Component Library framework, originally developed by qadram software S.L. (2004-2008). It enables building web applications using a component-based paradigm similar to Delphi's VCL.

**Branch `php84-migration`**: Modernized version compatible with PHP 7.4 - 8.4 and modern browsers.

## Development Environment

### Using DDEV (Recommended)
```bash
ddev start              # Start containers (PHP 8.4 supported)
ddev ssh                # Shell into web container
ddev xdebug on          # Enable debugging
ddev stop               # Stop containers
```
Access at: `http://vcl.ddev.site`

## Running Tests

Tests use PHPUnit and are located in `tests/testsource/`:
```bash
# From within ddev container
cd tests
php run_php_tests.bat   # Windows batch wrapper
```

Test configuration files:
- `tests/mysqlcfg.inc.php` - MySQL connection settings
- `tests/interbasecfg.inc.php` - InterBase connection settings

## Architecture

### Class Hierarchy
```
VCLObject (system.inc.php)  # Note: Named "Object" in legacy code, renamed for PHP 7.2+
  └── Persistent (classes.inc.php) - Session serialization
      └── Component (classes.inc.php) - Parent/child relationships
          ├── Control (controls.inc.php) - Visual elements
          │   └── FocusControl (forms.inc.php)
          │       └── CustomPage → Page, DataModule
          ├── CustomConnection (db.inc.php) - Database base
          │   └── MySQLDatabase, OracleDatabase, InterbaseDatabase
          └── DataSet (db.inc.php) - Query results
```

### Core Files Dependency Order
1. **vcl.inc.php** - Entry point, path calculation, `use_unit()` loader
2. **system.inc.php** - `Object` base class, `Input`/`InputParam` for request handling
3. **rtl.inc.php** - Utility functions (`boolToStr`, `textToHtml`, `redirect`)
4. **classes.inc.php** - `Component`, `Persistent`, `Collection`, XML `Filer`/`Reader`
5. **graphics.inc.php** - `Font`, `Brush`, `Pen`, `Canvas`
6. **controls.inc.php** - `Control` base with layout, events, styling
7. **forms.inc.php** - `Application`, `Page`, `Window`, `DataModule`

### Control Libraries
| File | Content |
|------|---------|
| stdctrls.inc.php | Edit, Button, Label, ListBox, ComboBox, CheckBox, Memo |
| extctrls.inc.php | Panel, GroupBox, Image, Shape, FlashObject |
| comctrls.inc.php | StringGrid, PageControl, Toolbar, TreeView, StatusBar |
| buttons.inc.php | BitBtn, SpeedButton variants |
| dbctrls.inc.php | DBEdit, DBCheckBox, DBComboBox, DBMemo |
| dbgrids.inc.php | DBGrid, DBStringGrid |
| menus.inc.php | MainMenu, PopupMenu, MenuItem |

### Database Drivers
| File | Classes |
|------|---------|
| mysql.inc.php | MySQLDatabase, MySQLTable, MySQLQuery, MySQLStoredProc |
| oracle.inc.php | OracleDatabase, OracleTable, OracleQuery |
| interbase.inc.php | InterbaseDatabase, InterbaseTable, InterbaseQuery |

## Key Patterns

### Property Magic Methods
Properties use `__get`/`__set` magic methods:
```php
// Looks for getCaption()/setCaption(), then readCaption()/writeCaption()
$control->Caption = "Text";
$value = $control->Caption;
```

### Component Creation (Programmatic)
For programmatically created pages (without XML resource files), you **must** call `preinit()` and `init()` before `show()`:

```php
require_once("vcl.inc.php");
use_unit("forms.inc.php");
use_unit("stdctrls.inc.php");

class MyPage extends Page {
    public $Button1 = null;

    function __construct($aowner = null) {
        parent::__construct($aowner);
        $this->Name = "MyPage";  // Required! Prevents JS errors

        $this->Button1 = new Button($this);
        $this->Button1->Name = "Button1";
        $this->Button1->Parent = $this;
        $this->Button1->OnClick = "Button1Click";
    }

    function Button1Click($sender, $params) {
        // Event handler
    }
}

global $application;
$page = new MyPage($application);
$page->preinit();  // Reads form values from POST
$page->init();     // Processes events, calls event handlers
$page->show();
```

**Critical**: Without `preinit()` and `init()`, form values won't be read and events won't fire!

### Session Persistence
Components automatically serialize changed properties to `$_SESSION` at shutdown and restore them on subsequent requests. Only properties that differ from defaults are stored.

### XML Component Definitions
Components can be defined in XML:
```xml
<OBJECT CLASS="Button" NAME="Button1">
  <PROPERTY NAME="Caption">Click Me</PROPERTY>
  <PROPERTY NAME="Left">10</PROPERTY>
</OBJECT>
```

## Package System

Component registration in `packages/`:
- **standard.package.php** - Core UI components
- **mysql.package.php** - MySQL database components
- **oracle.package.php** - Oracle database components
- **database.package.php** - All database components

External library assets in: `qooxdoo/`, `dynapi/`, `xajax/`, `smarty/`, `libchart/`

## Important Conventions

- All include files use `.inc.php` extension
- Use `use_unit()` instead of `require_once` for VCL modules
- Component names must match their class names for serialization
- Events are JavaScript-based (OnClick, OnChange, etc.) and generate client-side code
- Database connections trigger OnBeforeConnect/OnAfterConnect events
- Layout uses alignment constants: alNone, alTop, alBottom, alLeft, alRight, alClient

## PHP 8.4 Migration Notes (Branch: php84-migration)

### Completed Migrations
| Original | Replacement | Reason |
|----------|-------------|--------|
| `class Object` | `class VCLObject` | "Object" is reserved in PHP 7.2+ |
| `each($array)` | `foreach($array as $k => $v)` | Removed in PHP 8.0 |
| `$str{0}` | `$str[0]` | Curly brace syntax removed in PHP 8.0 |
| `mysql_*` | `mysqli_*` | mysql extension removed in PHP 7.0 |
| `split()` | `preg_split()` or `explode()` | Removed in PHP 7.0 |
| `utf8_decode()` | `mb_convert_encoding($s, 'ISO-8859-1', 'UTF-8')` | Deprecated in PHP 8.2 |
| `$HTTP_SERVER_VARS` | `$_SERVER` | Removed in PHP 5.4 |

### JavaScript Fixes for Modern Browsers
- Removed `var event = event || window.event` (causes syntax error in strict mode)
- Removed `document.all` and `document.layers` fallbacks
- Use `document.getElementById()` instead

### ADOdb
Updated from version 5.05 to 5.22.8 for PHP 8.x compatibility.
