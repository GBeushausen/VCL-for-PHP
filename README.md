# VCL for PHP 3.0

[![CI](https://github.com/GBeushausen/VCL-for-PHP/actions/workflows/ci.yml/badge.svg)](https://github.com/GBeushausen/VCL-for-PHP/actions/workflows/ci.yml)

A Delphi-inspired Visual Component Library for building web applications with PHP.

> **Note:** This is a fun project to demonstrate how to refactor ancient legacy code to modern PHP standards. The code is **not production ready**.

## What is this?

VCL for PHP was originally developed by qadram software S.L. (2004-2008) as a RAD (Rapid Application Development) framework that brought Delphi's component-based paradigm to PHP web development.

This repository contains a modernized version that has been refactored to work with PHP 8.4+, demonstrating:

- Migration from PHP 4/5 patterns to PHP 8.4 property hooks
- Updating deprecated functions (`each()`, `mysql_*`, `split()`, etc.)
- Converting from procedural to object-oriented code with proper namespaces
- Using PSR-4 autoloading via Composer
- Modern HTML5 output instead of legacy HTML 4/XHTML

## Requirements

- PHP 8.4+
- Composer

## Installation

```bash
git clone https://github.com/GBeushausen/VCL-for-PHP.git
cd VCL-for-PHP
composer install
npm install          # Optional: for htmx AJAX support
```

## Quick Start

```php
<?php
declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use VCL\Forms\Application;
use VCL\Forms\Page;
use VCL\StdCtrls\Label;
use VCL\StdCtrls\Button;

class MyPage extends Page
{
    public ?Label $Label1 = null;
    public ?Button $Button1 = null;

    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);
        $this->Name = "MyPage";

        $this->Label1 = new Label($this);
        $this->Label1->Name = "Label1";
        $this->Label1->Parent = $this;
        $this->Label1->Caption = "Hello World!";

        $this->Button1 = new Button($this);
        $this->Button1->Name = "Button1";
        $this->Button1->Parent = $this;
        $this->Button1->Caption = "Click Me";
        $this->Button1->OnClick = "Button1Click";
    }

    public function Button1Click(object $sender, array $params): void
    {
        $this->Label1->Caption = "Button clicked!";
    }
}

$application = Application::getInstance();
$page = new MyPage($application);
$page->preinit();
$page->init();
$page->show();
```

## Documentation

See the [docs/](docs/) folder for component documentation.

## Development with DDEV

```bash
ddev start
ddev launch
```

Access at: `http://vcl.ddev.site`

## Project Status

This is an educational/experimental project. Key areas that have been modernized:

- [x] PHP 8.4 property hooks
- [x] PSR-4 autoloading
- [x] Namespaced classes
- [x] HTML5 output with viewport
- [x] Modern JavaScript (no `document.all`, `document.layers`)
- [x] mysqli instead of deprecated mysql_*
- [x] Full test coverage (611 tests, 736 assertions)
- [x] Complete component migration
- [x] PHPStan static analysis (level 5, 0 errors)
- [x] GitHub Actions CI

## Quality Assurance

```bash
# Run tests
composer test

# Run static analysis
composer analyse

# Run both
composer check
```

## License

LGPL v2.1 - GNU Lesser General Public License

## Copyright

- Original: Copyright (c) 2004-2008 qadram software S.L.
- Modernization: Copyright (c) 2026 Gunnar Beushausen
