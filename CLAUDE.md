# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

VCL for PHP 2.0 is a Delphi-inspired Visual Component Library framework for PHP 5.x, developed by qadram software S.L. (2004-2008). It enables building web applications using a component-based paradigm similar to Delphi's VCL.

## Development Environment

### Using DDEV (Recommended)
```bash
ddev start              # Start PHP 5.6 + MySQL 5.5 containers
ddev ssh                # Shell into web container
ddev xdebug on          # Enable debugging
ddev stop               # Stop containers
```
Access at: `http://vcl.ddev.site`

### Using Legacy Docker
```bash
docker-compose -f docker-compose.legacy-php56.yml up -d
```
Access at: `http://localhost:8080`

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
Object (system.inc.php)
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

### Component Creation
```php
require_once("vcl/vcl.inc.php");
use_unit("forms.inc.php");
use_unit("stdctrls.inc.php");

$Application = new Application();

class MyPage extends Page {
    function MyPageBeforeShow(&$sender, &$params) {
        // Initialize
    }
}

$page = new MyPage(null);
$page->show();
```

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
