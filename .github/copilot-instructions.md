# Copilot coding instructions (VCL for PHP)

## Big picture
- This repo is **VCL for PHP 3.0**: a Delphi‑inspired, component-based web UI framework.
- Target is **PHP 8.4+ only** (property hooks, enums). Most work happens under `src/VCL/` (PSR-4 `VCL\\*`).
- Backwards compatibility matters: legacy entrypoints/wrappers (`vcl.inc.php`, `src/bootstrap.php`) and legacy aliases/constants are intentionally kept.

## Dev workflow
- Install: `composer install` (required)
- Optional for local htmx asset: `npm install` (otherwise a CDN fallback is used)
- Tests: `composer test` (PHPUnit; suites in `tests/Unit` + `tests/Integration`, bootstrap `tests/bootstrap.php`)
- Static analysis: `composer analyse` (PHPStan level 5; property-hooks have intentional ignores in `phpstan.neon`)
- Combined: `composer check`
- DDEV (recommended): `ddev start`, `ddev ssh`, `ddev xdebug on/off` (see `.ddev/`)

## Core runtime model
- Typical app flow: create `Application::getInstance()`, instantiate a `Page` subclass, then call `preinit()`, `init()`, `show()` (see `demo_simple.php`, `demo_advanced.php`, `docs/README.md`).
- Components are owned objects: `new Button($this)` registers with owner; `Name` must be unique within an owner (see `src/VCL/Core/Component.php`).
- “Delphi-style” public properties use PascalCase property hooks backed by `$_fields` (e.g. `protected string $_caption` + `public string $Caption { ... }`).
- Keep legacy getters/setters alongside property hooks for compatibility (pattern at bottom of `src/VCL/Forms/Page.php`).

## Events & handlers
- Event properties typically store a **method name string** (e.g. `$Button->OnClick = 'Button1Click'`).
- `Component::callEvent()` dispatches to the owner’s method when appropriate (see `src/VCL/Core/Component.php`).

## AJAX: prefer htmx over legacy xajax
- Enable on a page via `Page->UseHtmx = true` (optionally `UseHtmxDebug = true`).
- htmx requests are handled by `Page::processHtmx()` using `VCL\\Ajax\\HtmxHandler` (see `src/VCL/Forms/Page.php`, `src/VCL/Ajax/HtmxHandler.php`).
- htmx event handlers should return a **string HTML fragment** (or `null` for side-effects); the handler response is sent as `text/html`.
- When generating htmx attributes, use `Component::generateHtmxEvent()` / `generateHtmxSubmit()` / `htmxCall()` rather than hand-rolling attributes (see `src/VCL/Core/Component.php`).

## Legacy dependencies (avoid for new code)
- `adodb/` exists as legacy baggage and is planned to be refactored away; do **not** add new code that depends on it.
- `smarty/smarty` is legacy and **not supported**; don’t add or extend template-engine integration.

## Where to change things
- New/modern code goes into `src/VCL/**` under the correct package namespace (e.g. `StdCtrls`, `ExtCtrls`, `Forms`).
- Global/legacy bridge helpers live in `src/bootstrap.php` and wrapper `vcl.inc.php`; be careful not to break `use_unit()` mappings and legacy alias loading.
- Constants are loaded via Composer `autoload.files` in `composer.json` (e.g. `src/VCL/*/constants.php`); add new constants to existing files unless you intentionally introduce a new autoloaded constants file.
