# Security Migration Guide

This guide helps you migrate existing VCL code to use the new security features.

## Overview

VCL 3.0 introduces a comprehensive security layer:

| Feature | Old Way | New Way |
|---------|---------|---------|
| HTML escaping | `htmlspecialchars()` | `Escaper::html()` |
| SQL parameters | `QuoteStr()` | Prepared statements |
| User input | Direct `$_POST` access | `InputValidator` |
| HTML sanitization | Manual filtering | `Sanitizer` |

## Step 1: Replace HTML Escaping

### Before (Vulnerable)
```php
// Direct output - XSS vulnerable
echo $username;
echo "<div title='$userInput'>";

// Inconsistent escaping
echo htmlspecialchars($data);  // Missing ENT_QUOTES
```

### After (Secure)
```php
use VCL\Security\Escaper;

// All output properly escaped
echo Escaper::html($username);
echo "<div title='" . Escaper::attr($userInput) . "'>";

// Consistent, context-aware escaping
echo Escaper::html($data);
```

### Migration Checklist
- [ ] Replace all `echo $variable` with `echo Escaper::html($variable)`
- [ ] Replace `htmlspecialchars()` with `Escaper::html()` or `Escaper::attr()`
- [ ] Check all attribute outputs use `Escaper::attr()`

## Step 2: Migrate SQL Queries

### Before (Vulnerable)
```php
// String concatenation - SQL injection vulnerable
$sql = "SELECT * FROM users WHERE id = " . $_GET['id'];
$db->Execute($sql);

// QuoteStr - better but still risky
$sql = "SELECT * FROM users WHERE name = " . $db->QuoteStr($name);
$db->Execute($sql);
```

### After (Secure)
```php
// Prepared statements with parameter binding
$db->Execute(
    "SELECT * FROM users WHERE id = ?",
    [$_GET['id']]
);

// Multiple parameters
$db->Execute(
    "SELECT * FROM users WHERE name = ? AND status = ?",
    [$name, $status]
);
```

### MySQLTable Operations

The `MySQLTable` class now uses prepared statements internally:

```php
// These are now secure automatically
$table->Open();
$table->Fields['name'] = $userInput;  // Escaped via prepared statement
$table->Post();
```

### Migration Checklist
- [ ] Replace all `$db->Execute($sql . $variable)` with parameterized queries
- [ ] Replace `QuoteStr()` calls with prepared statement parameters
- [ ] Review all custom SQL queries for string concatenation

## Step 3: Add Input Validation

### Before (Vulnerable)
```php
// Direct superglobal access
$page = $_GET['page'];
$email = $_POST['email'];
$controlName = $_POST['_vcl_control'];
```

### After (Secure)
```php
use VCL\Security\InputValidator;

$validator = new InputValidator();

// Validated integer with range
$page = $validator->validateInteger($_GET['page'] ?? 1, min: 1, max: 1000);

// Validated email
$email = $validator->validateEmail($_POST['email'] ?? '');

// Validated control name (for htmx/AJAX)
$controlName = $validator->validateControlName($_POST['_vcl_control'] ?? '');
```

### Using VCL Input Class
```php
use VCL\Core\Input;

$input = new Input();

// Type-safe access with validation
$page = $input->get('page')?->asInteger() ?? 1;
$name = $input->post('name')?->asString() ?? '';
```

### Migration Checklist
- [ ] Replace direct `$_GET`, `$_POST` access with `Input` class
- [ ] Add `InputValidator` for control/event names
- [ ] Validate all user-provided URLs with `validateUrl()`
- [ ] Add range validation for numeric inputs

## Step 4: Sanitize Rich Text

### Before (Vulnerable)
```php
// Storing unsanitized HTML
$comment = $_POST['comment'];
$db->Execute("INSERT INTO comments (text) VALUES (?)", [$comment]);

// Outputting unsanitized HTML
echo $comment;  // XSS if contains <script>
```

### After (Secure)
```php
use VCL\Security\Sanitizer;

$sanitizer = new Sanitizer();

// Sanitize before storing
$comment = $sanitizer->sanitizeRichText($_POST['comment']);
$db->Execute("INSERT INTO comments (text) VALUES (?)", [$comment]);

// Or sanitize on output
echo $sanitizer->sanitizeRichText($comment);
```

### Sanitization Levels

| Method | Allows | Use Case |
|--------|--------|----------|
| `strip()` | Nothing | Plain text only |
| `sanitize()` | `<br>` | Basic line breaks |
| `sanitizeRichText()` | Basic formatting, links | Comments, posts |
| `sanitizeFull()` | Headers, tables, images | WYSIWYG editors |

### Migration Checklist
- [ ] Identify all user HTML input fields
- [ ] Apply appropriate sanitization level
- [ ] Sanitize on input OR output, document which

## Step 5: Secure JavaScript Output

### Before (Vulnerable)
```php
// Direct variable in JavaScript - XSS vulnerable
echo "<script>var name = '$username';</script>";
echo "<button onclick=\"doSomething('$param')\">";
```

### After (Secure)
```php
use VCL\Security\Escaper;

// JSON-encoded for JavaScript context
echo "<script>var name = " . Escaper::js($username) . ";</script>";

// For inline JS string literals
echo "<button onclick=\"doSomething('" . Escaper::jsString($param) . "')\">";

// For HTML attributes containing JS
$safeName = Escaper::id($controlName);
echo "<button onclick=\"{$safeName}_click()\">";
```

### Migration Checklist
- [ ] Replace inline JS variables with `Escaper::js()`
- [ ] Replace inline JS strings with `Escaper::jsString()`
- [ ] Use `Escaper::id()` for dynamically generated function names

## Step 6: Validate URLs

### Before (Vulnerable)
```php
// Redirect without validation - open redirect
header("Location: " . $_GET['next']);

// Link without validation - javascript: protocol XSS
echo "<a href='" . $userUrl . "'>";
```

### After (Secure)
```php
use VCL\Security\InputValidator;
use VCL\Security\Escaper;

$validator = new InputValidator();

// Validate redirect URL
$next = $validator->validateUrl($_GET['next'] ?? '/');
header("Location: " . $next);

// Escape URL in href
echo "<a href='" . Escaper::urlAttr($userUrl) . "'>";
```

### URL Validation Options
```php
// Only allow HTTPS
$validator->validateUrl($url, allowedSchemes: ['https']);

// Whitelist specific hosts
$validator->validateUrl($url, allowedHosts: ['example.com', '*.trusted.com']);

// Disallow relative URLs
$validator->validateUrl($url, allowRelative: false);
```

### Migration Checklist
- [ ] Validate all redirect URLs
- [ ] Use `Escaper::urlAttr()` for all user-provided URLs in HTML
- [ ] Review `header("Location: ...")` calls

## Step 7: Update CSS Output

### Before (Potentially Vulnerable)
```php
// Direct CSS - can inject expression() or url(javascript:)
echo "style='color: $userColor'";
```

### After (Secure)
```php
use VCL\Security\Escaper;

// Validated CSS value
echo "style='color: " . Escaper::css($userColor) . "'";

// For colors specifically
echo "style='color: " . Escaper::cssColor($userColor) . "'";
```

### Allowed CSS Values
- Hex colors: `#fff`, `#aabbcc`
- Named colors: `red`, `blue`, `transparent`
- RGB/RGBA: `rgb(255,0,0)`, `rgba(0,0,0,0.5)`
- Lengths: `10px`, `1.5em`, `100%`
- Keywords: `auto`, `none`, `inherit`
- Safe `url()`: `url(https://...)`, `url(/path/to/file)`

### Migration Checklist
- [ ] Replace direct CSS variable output with `Escaper::css()`
- [ ] Use `Escaper::cssColor()` for color-specific values

## Quick Reference

### Escaper Methods

| Method | Context | Example Output |
|--------|---------|----------------|
| `html()` | HTML text | `&lt;script&gt;` |
| `attr()` | HTML attributes | `&quot;onclick=...` |
| `js()` | JS values | `"\u003cscript\u003e"` |
| `jsString()` | JS strings (no quotes) | `\u003cscript\u003e` |
| `css()` | CSS values | `red` or `''` (invalid) |
| `cssColor()` | CSS colors | `#ff0000` or `''` |
| `url()` | URL encoding | `hello%20world` |
| `urlAttr()` | URL in href/src | `#` (for javascript:) |
| `id()` | HTML IDs | `my_safe_id` |

### InputValidator Methods

| Method | Validates | Throws |
|--------|-----------|--------|
| `validateControlName()` | VCL control names | `SecurityException` |
| `validateEventName()` | VCL event names | `SecurityException` |
| `validateUrl()` | Safe URLs | `SecurityException` |
| `validateEmail()` | Email addresses | `SecurityException` |
| `validateInteger()` | Integers with range | `SecurityException` |
| `validateString()` | Strings with length | `SecurityException` |
| `validatePattern()` | Regex pattern match | `SecurityException` |

### Common Patterns

```php
use VCL\Security\Escaper;
use VCL\Security\Sanitizer;
use VCL\Security\InputValidator;

// Setup
$sanitizer = new Sanitizer();
$validator = new InputValidator();

// Safe user display
echo '<p>Hello, ' . Escaper::html($username) . '</p>';

// Safe form value
echo '<input value="' . Escaper::attr($value) . '">';

// Safe link
echo '<a href="' . Escaper::urlAttr($url) . '">' . Escaper::html($text) . '</a>';

// Safe JavaScript data
echo '<script>var config = ' . Escaper::js($config) . ';</script>';

// Safe rich text comment
echo '<div class="comment">' . $sanitizer->sanitizeRichText($comment) . '</div>';

// Safe database query
$db->Execute("SELECT * FROM users WHERE id = ?", [$validator->validateInteger($id)]);
```

## Testing Your Migration

After migration, run the security test suite:

```bash
# All security tests
vendor/bin/phpunit tests/Unit/Security/

# XSS payload tests
vendor/bin/phpunit tests/Unit/Security/XssPayloadTest.php

# SQL injection tests
vendor/bin/phpunit tests/Unit/Security/SqlInjectionTest.php
```

## Deprecation Warnings

The following methods now trigger deprecation warnings:

- `Helpers::dbcsUnserialize()` - Use JSON instead
- `Helpers::safeUnserialize()` - Use JSON instead
- `MySQLDatabase::QuoteStr()` - Use prepared statements

These will be removed in a future version.
