# VCL for PHP 2.0 Documentation

VCL for PHP is a Delphi-inspired Visual Component Library framework for building web applications with a component-based architecture.

Originally developed by qadram software S.L. (2004-2008), this version has been modernized for PHP 8.4+.

## Quick Start

See [demo_simple.php](../demo_simple.php) and [demo_advanced.php](../demo_advanced.php) for working examples.

### Minimal Example

```php
<?php
declare(strict_types=1);

require_once(__DIR__ . '/vendor/autoload.php');

use VCL\Forms\Page;
use VCL\Forms\Application;
use VCL\StdCtrls\Label;
use VCL\StdCtrls\Edit;
use VCL\StdCtrls\Button;

class MyPage extends Page
{
    public ?Label $Label1 = null;
    public ?Edit $Edit1 = null;
    public ?Button $Button1 = null;

    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);

        $this->Name = "MyPage";
        $this->Caption = "My Page Title";

        $this->Label1 = new Label($this);
        $this->Label1->Name = "Label1";
        $this->Label1->Parent = $this;
        $this->Label1->Left = 20;
        $this->Label1->Top = 20;
        $this->Label1->Caption = "Enter your name:";

        $this->Edit1 = new Edit($this);
        $this->Edit1->Name = "Edit1";
        $this->Edit1->Parent = $this;
        $this->Edit1->Left = 20;
        $this->Edit1->Top = 50;
        $this->Edit1->Width = 200;

        $this->Button1 = new Button($this);
        $this->Button1->Name = "Button1";
        $this->Button1->Parent = $this;
        $this->Button1->Left = 230;
        $this->Button1->Top = 48;
        $this->Button1->Caption = "Submit";
        $this->Button1->OnClick = "Button1Click";
    }

    public function Button1Click(object $sender, array $params): void
    {
        $name = $this->Edit1->Text;
        $this->Label1->Caption = "Hello, " . htmlspecialchars($name);
    }
}

$application = Application::getInstance();
$page = new MyPage($application);
$page->preinit();  // Read form values from POST
$page->init();     // Process events
$page->show();     // Render HTML
```

## Component Reference

### Forms

- [Application](components/Application.md) - Application singleton
- [Page](components/Page.md) - Main application page
- [DataModule](components/DataModule.md) - Non-visual container
- [HiddenField](components/HiddenField.md) - Hidden form field

### Standard Controls (StdCtrls)

- [Label](components/Label.md) - Text display
- [Edit](components/Edit.md) - Single-line text input
- [Button](components/Button.md) - Push button
- [CheckBox](components/CheckBox.md) - Boolean checkbox
- [CheckListBox](components/CheckListBox.md) - List with checkboxes
- [RadioButton](components/RadioButton.md) - Radio button selection
- [ComboBox](components/ComboBox.md) - Dropdown selection
- [Memo](components/Memo.md) - Multi-line text input

### Extended Controls (ExtCtrls)

- [Panel](components/Panel.md) - Container control
- [Image](components/Image.md) - Image display
- [Timer](components/Timer.md) - Client-side timer
- [Shape](components/Shape.md) - Geometric shapes
- [Bevel](components/Bevel.md) - Beveled boxes/lines
- [PaintBox](components/PaintBox.md) - Custom drawing canvas

### Common Controls (ComCtrls)

- [ProgressBar](components/ProgressBar.md) - Progress indicator
- [TrackBar](components/TrackBar.md) - Slider control
- [DateTimePicker](components/DateTimePicker.md) - Date/time selection
- [MonthCalendar](components/MonthCalendar.md) - Calendar control
- [Pager](components/Pager.md) - Data pagination

### Buttons

- [BitBtn](components/BitBtn.md) - Button with image
- [SpeedButton](components/SpeedButton.md) - Toolbar toggle button

### Data Controls (DBCtrls)

- [DBGrid](components/DBGrid.md) - Data grid
- [DBRepeater](components/DBRepeater.md) - Repeat controls for records
- [DBPaginator](components/DBPaginator.md) - Dataset navigation

### Database (MySQL)

- [MySQLDatabase](components/MySQLDatabase.md) - MySQL connection
- [MySQLQuery](components/MySQLQuery.md) - SQL query dataset
- [MySQLTable](components/MySQLTable.md) - Table dataset
- [Datasource](components/Datasource.md) - Links controls to datasets

### Menus

- [MainMenu](components/MainMenu.md) - Menu bar
- [PopupMenu](components/PopupMenu.md) - Context menu

### Authentication

- [BasicAuthentication](components/BasicAuthentication.md) - HTTP Basic Auth

### Styles

- [StyleSheet](components/StyleSheet.md) - CSS stylesheet loader

### Actions

- [ActionList](components/ActionList.md) - URL-based action routing

### Graphics

- [Font](components/Font.md) - Font properties
- [Brush](components/Brush.md) - Fill properties
- [Pen](components/Pen.md) - Line/outline properties
- [ImageList](components/ImageList.md) - Image path collection

## Page Lifecycle

1. **Constructor** - Create components
2. **preinit()** - Read submitted form values into component properties
3. **init()** - Process events (calls OnClick handlers etc.)
4. **show()** - Render HTML output

## Requirements

- PHP 8.4+
- Composer

## License

LGPL v2.1 - GNU Lesser General Public License

## Copyright

- Original: Copyright (c) 2004-2008 qadram software S.L.
- Modernization: Copyright (c) 2026 Gunnar Beushausen
