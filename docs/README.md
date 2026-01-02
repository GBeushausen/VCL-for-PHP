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

- [Page](components/Page.md)
- [Label](components/Label.md)
- [Edit](components/Edit.md)
- [Button](components/Button.md)
- [CheckBox](components/CheckBox.md)
- [RadioButton](components/RadioButton.md)
- [ComboBox](components/ComboBox.md)
- [Memo](components/Memo.md)

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

Copyright (c) 2004-2008 qadram software S.L.
