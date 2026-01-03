# VCL Security Documentation

This documentation describes the security features of the VCL Framework.

## Table of Contents

1. [Overview](#overview)
2. [Escaper - Context-Aware Escaping](#escaper---context-aware-escaping)
3. [Sanitizer - HTML Sanitization](#sanitizer---html-sanitization)
4. [InputValidator - Input Validation](#inputvalidator---input-validation)
5. [Constraints - Symfony Validator](#constraints---symfony-validator)
6. [Database Security](#database-security)
7. [CSRF Protection](#csrf-protection)
8. [Best Practices](#best-practices)

---

## Overview

VCL includes a multi-layered security architecture:

```
┌─────────────────────────────────────────────────────────────┐
│                        User Input                           │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                    InputValidator                            │
│  - Validates structure and format                           │
│  - Throws SecurityException for invalid data                │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                      Sanitizer                               │
│  - Removes dangerous HTML elements                          │
│  - Allows safe formatting                                   │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│              Business Logic / Database                      │
│  - Prepared statements for SQL                              │
│  - Validated column names                                   │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                       Escaper                                │
│  - Context-aware output escaping                            │
│  - HTML, JS, CSS, URL contexts                              │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                        Output                                │
└─────────────────────────────────────────────────────────────┘
```

### Namespace Structure

```
VCL\Security\
├── Escaper.php                 # Output escaping
├── Sanitizer.php               # HTML sanitization
├── InputValidator.php          # Input validation
├── Constraints/                # Symfony Validator Constraints
│   ├── ValidControlName.php
│   ├── ValidControlNameValidator.php
│   ├── ValidEventName.php
│   ├── ValidEventNameValidator.php
│   ├── SafeUrl.php
│   └── SafeUrlValidator.php
└── Exception/
    └── SecurityException.php   # Security-specific exceptions
```

---

## Escaper - Context-Aware Escaping

The `Escaper` converts potentially dangerous characters into safe representations, depending on the output context.

### Installation

The Escaper is part of the VCL framework and requires no additional installation.

```php
use VCL\Security\Escaper;
```

### Usage

#### Static Methods (recommended for simple usage)

```php
// HTML text
echo Escaper::html($userInput);

// HTML attributes
echo Escaper::attr($userInput);

// JavaScript values
echo Escaper::js($data);

// JavaScript strings (without quotes)
echo Escaper::jsString($text);

// CSS values
echo Escaper::css($color);

// CSS colors
echo Escaper::cssColor($color);

// URL encoding
echo Escaper::url($param);

// URLs in href/src attributes
echo Escaper::urlAttr($url);

// HTML IDs
echo Escaper::id($name);
```

#### Instance Methods (for Dependency Injection / Tests)

```php
$escaper = new Escaper();

echo $escaper->escapeHtml($input);
echo $escaper->escapeAttr($input);
echo $escaper->escapeJs($data);
echo $escaper->escapeJsString($text);
echo $escaper->escapeCss($value);
echo $escaper->escapeCssColor($color);
echo $escaper->escapeUrl($param);
echo $escaper->escapeUrlAttr($url);
echo $escaper->escapeId($name);
```

### Method Reference

#### `html(string $string): string`

Escapes special characters for HTML text content.

```php
$input = '<script>alert("XSS")</script>';
echo Escaper::html($input);
// Output: &lt;script&gt;alert(&quot;XSS&quot;)&lt;/script&gt;
```

**Escaped characters:**
- `<` → `&lt;`
- `>` → `&gt;`
- `"` → `&quot;`
- `'` → `&apos;`
- `&` → `&amp;`

#### `attr(string $string): string`

Escapes for HTML attributes. Functionally identical to `html()`, but semantically clear.

```php
echo '<input value="' . Escaper::attr($userInput) . '">';
echo "<div title='" . Escaper::attr($title) . "'>";
```

#### `js(mixed $value): string`

Encodes values for JavaScript context using JSON.

```php
$data = ['name' => '<script>', 'id' => 123];
echo '<script>var config = ' . Escaper::js($data) . ';</script>';
// Output: <script>var config = {"name":"\u003Cscript\u003E","id":123};</script>
```

**Supported types:**
- Strings
- Integers
- Floats
- Booleans
- Arrays
- Objects (as JSON)

**Security flags:**
- `JSON_HEX_TAG` - `<` and `>` are Unicode-escaped
- `JSON_HEX_APOS` - `'` is Unicode-escaped
- `JSON_HEX_QUOT` - `"` is Unicode-escaped
- `JSON_HEX_AMP` - `&` is Unicode-escaped

#### `jsString(string $string): string`

Escapes a string for JavaScript string literals, without surrounding quotes.

```php
echo "var name = '" . Escaper::jsString($name) . "';";
echo 'onclick="alert(\'' . Escaper::jsString($message) . '\')"';
```

#### `css(string $value, string $default = ''): string`

Validates and returns safe CSS values.

```php
echo 'style="color: ' . Escaper::css($color) . '"';
echo 'style="width: ' . Escaper::css($width) . '"';
```

**Allowed values:**
- Hex colors: `#fff`, `#aabbcc`, `#aabbccdd`
- Named colors: `red`, `blue`, `transparent`
- RGB/RGBA: `rgb(255,0,0)`, `rgba(0,0,0,0.5)`
- HSL/HSLA: `hsl(120,100%,50%)`
- Lengths: `10px`, `1.5em`, `100%`, `50vh`
- Keywords: `auto`, `none`, `inherit`, `initial`, `unset`, `normal`, `bold`, `italic`
- Safe URLs: `url(https://example.com/image.png)`, `url(/path/to/file)`

**Blocked values:**
- `expression()` - IE CSS expression
- `javascript:` URLs
- `behavior:` - IE HTC
- `-moz-binding:` - Firefox XBL
- `@import`, `@charset`

#### `cssColor(string $color, string $default = ''): string`

Validates specifically color values.

```php
echo 'style="background-color: ' . Escaper::cssColor($bgColor) . '"';
```

#### `url(string $string): string`

URL-encodes a string (for query parameters).

```php
$url = '/search?q=' . Escaper::url($searchTerm);
```

#### `urlAttr(string $url, string $default = '#'): string`

Validates URLs for `href`/`src` attributes. Blocks dangerous protocols.

```php
echo '<a href="' . Escaper::urlAttr($userUrl) . '">';
echo '<img src="' . Escaper::urlAttr($imageUrl) . '">';
```

**Allowed URLs:**
- `https://example.com`
- `http://example.com`
- `mailto:user@example.com`
- `/relative/path`
- `./relative`
- `../parent`
- `#anchor`

**Blocked URLs:**
- `javascript:alert(1)`
- `data:text/html,...`
- `vbscript:...`
- `file:///...`

#### `id(string $id): string`

Creates safe HTML IDs.

```php
echo '<div id="' . Escaper::id($componentName) . '">';
```

**Transformations:**
- Invalid characters are replaced with `_`
- IDs starting with a number get a `_` prefix

### Testing / Mocking

```php
// Custom instance for tests
$mock = $this->createMock(Escaper::class);
$mock->method('escapeHtml')->willReturn('mocked');

Escaper::setInstance($mock);

// Reset after test
Escaper::resetInstance();
```

---

## Sanitizer - HTML Sanitization

The `Sanitizer` removes dangerous HTML elements and attributes while allowing safe formatting.

### Installation

Requires `symfony/html-sanitizer`:

```bash
composer require symfony/html-sanitizer
```

### Usage

```php
use VCL\Security\Sanitizer;

$sanitizer = new Sanitizer();
```

### Method Reference

#### `sanitize(string $html): string`

Minimal sanitization - removes almost all tags.

```php
$input = '<p>Hello</p><script>alert(1)</script>';
echo $sanitizer->sanitize($input);
// Output: Hello
```

**Allowed tags:** `<br>`

#### `sanitizeRichText(string $html): string`

Allows basic formatting for comments, posts, etc.

```php
$input = '<p><b>Bold</b> and <a href="http://example.com" onclick="evil()">link</a></p>';
echo $sanitizer->sanitizeRichText($input);
// Output: <p><b>Bold</b> and <a href="http://example.com" rel="noopener noreferrer">link</a></p>
```

**Allowed tags:**
- Formatting: `<b>`, `<i>`, `<u>`, `<s>`, `<strong>`, `<em>`
- Structure: `<p>`, `<br>`, `<hr>`
- Lists: `<ul>`, `<ol>`, `<li>`
- Links: `<a>` (with `href`, automatically adds `rel="noopener noreferrer"`)
- Quotes: `<blockquote>`

**Removed elements:**
- `<script>`, `<style>`, `<iframe>`, `<object>`, `<embed>`
- Event handlers: `onclick`, `onerror`, `onload`, etc.
- `javascript:` URLs

#### `sanitizeFull(string $html): string`

Extensive formatting for WYSIWYG editors.

```php
$html = $sanitizer->sanitizeFull($wysiwygContent);
```

**Additionally allowed:**
- Headings: `<h1>` - `<h6>`
- Tables: `<table>`, `<tr>`, `<td>`, `<th>`, `<thead>`, `<tbody>`
- Images: `<img>` (with `src`, `alt`, `width`, `height`)
- Code: `<pre>`, `<code>`
- Definitions: `<dl>`, `<dt>`, `<dd>`

#### `strip(string $html): string`

Removes all HTML tags completely.

```php
$input = '<p>Hello <b>World</b></p>';
echo $sanitizer->strip($input);
// Output: Hello World
```

### Instance Management

```php
// Custom instance for DI
$sanitizer = new Sanitizer();

// For tests: set mock
Sanitizer::setInstance($mockSanitizer);

// Reset
Sanitizer::resetInstance();
```

---

## InputValidator - Input Validation

The `InputValidator` checks user input and throws exceptions for invalid data.

### Installation

Requires `symfony/validator`:

```bash
composer require symfony/validator
```

### Usage

```php
use VCL\Security\InputValidator;
use VCL\Security\Exception\SecurityException;

$validator = new InputValidator();

try {
    $controlName = $validator->validateControlName($_POST['control']);
} catch (SecurityException $e) {
    // Invalid input
}
```

### Method Reference

#### Control and Event Names

```php
// Validates VCL control names
$name = $validator->validateControlName($input);
// Allowed: Button1, _private, my_control, Control-Name
// Pattern: /^[a-zA-Z_][a-zA-Z0-9_-]*$/

// Validates VCL event names
$event = $validator->validateEventName($input);
// Allowed: onClick, onChange, onCustomEvent
// Pattern: /^[a-zA-Z_][a-zA-Z0-9_]*$/

// Boolean check (does not throw exception)
if ($validator->isValidControlName($input)) { ... }
if ($validator->isValidEventName($input)) { ... }
```

#### URLs

```php
// Standard validation (http, https allowed)
$url = $validator->validateUrl($input);

// Only allow HTTPS
$url = $validator->validateUrl($input, allowedSchemes: ['https']);

// Allow specific hosts
$url = $validator->validateUrl($input, allowedHosts: ['example.com', '*.trusted.com']);

// Disallow relative URLs
$url = $validator->validateUrl($input, allowRelative: false);

// Boolean check
if ($validator->isValidUrl($input)) { ... }
```

#### Email

```php
$email = $validator->validateEmail($input);

if ($validator->isValidEmail($input)) { ... }
```

#### Integer

```php
// Simple validation
$id = $validator->validateInteger($input);

// With range check
$page = $validator->validateInteger($input, min: 1, max: 1000);

// String is converted to integer
$id = $validator->validateInteger("42");  // Returns: 42
```

#### Strings

```php
// With length limits
$name = $validator->validateString($input, minLength: 1, maxLength: 100);

// Maximum length only
$comment = $validator->validateString($input, maxLength: 5000);
```

#### Pattern Matching

```php
// Custom regex pattern
$code = $validator->validatePattern($input, '/^[A-Z]{2}\d{4}$/');
```

#### Generic Symfony Constraints

```php
use Symfony\Component\Validator\Constraints as Assert;

// Validation with arbitrary constraints
$violations = $validator->validate($value, [
    new Assert\NotBlank(),
    new Assert\Length(['min' => 3, 'max' => 50]),
]);

// Boolean check
$isValid = $validator->isValid($value, [
    new Assert\Email(),
]);

// Error messages as array
$errors = $validator->getErrors($value, [
    new Assert\NotBlank(),
]);
```

### Singleton Pattern

```php
// Singleton instance
$validator = InputValidator::getInstance();

// Set custom instance (for tests)
InputValidator::setInstance($mockValidator);

// Reset
InputValidator::resetInstance();
```

---

## Constraints - Symfony Validator

VCL provides custom Symfony Validator constraints.

### ValidControlName

Validates VCL control names.

```php
use VCL\Security\Constraints\ValidControlName;

// As attribute
class MyDTO
{
    #[ValidControlName]
    public string $controlName;
}

// Programmatically
$constraint = new ValidControlName();
$violations = $validator->validate($value, [$constraint]);

// With whitelist (strict mode)
$constraint = new ValidControlName(
    allowedNames: ['Button1', 'Edit1', 'Label1'],
    strict: true
);
```

**Options:**
- `allowedNames` - Array of allowed names
- `strict` - If true, name must be in allowedNames
- `message` - Custom error message

### ValidEventName

Validates VCL event names.

```php
use VCL\Security\Constraints\ValidEventName;

#[ValidEventName]
public string $eventName;

// With custom event whitelist
$constraint = new ValidEventName(
    allowedEvents: ['onClick', 'onChange'],
    strict: true
);
```

**Default events (when strict without allowedEvents):**
- `onClick`, `onChange`, `onSubmit`, `onFocus`, `onBlur`
- `onKeyDown`, `onKeyUp`, `onKeyPress`
- `onMouseOver`, `onMouseOut`, `onMouseDown`, `onMouseUp`
- `onLoad`, `onUnload`
- `onBeforeAjaxProcess`, `onAfterAjaxProcess`
- `onShow`, `onHide`, `onActivate`, `onDeactivate`

### SafeUrl

Validates URLs and blocks dangerous protocols.

```php
use VCL\Security\Constraints\SafeUrl;

// Standard (http, https, relative URLs allowed)
#[SafeUrl]
public string $website;

// HTTPS only
#[SafeUrl(allowedSchemes: ['https'])]
public string $secureUrl;

// With host whitelist
#[SafeUrl(allowedHosts: ['example.com', '*.trusted.com'])]
public string $trustedUrl;

// No relative URLs
#[SafeUrl(allowRelative: false)]
public string $absoluteUrl;
```

**Always blocked protocols:**
- `javascript:`
- `data:`
- `vbscript:`
- `file:`

---

## Database Security

### Prepared Statements

VCL uses mysqli prepared statements for all database operations.

```php
// Simple query with parameters
$db->Execute(
    "SELECT * FROM users WHERE id = ? AND status = ?",
    [$userId, $status]
);

// Insert
$db->Execute(
    "INSERT INTO logs (user_id, action, timestamp) VALUES (?, ?, ?)",
    [$userId, $action, time()]
);

// Update
$db->Execute(
    "UPDATE users SET email = ?, updated_at = ? WHERE id = ?",
    [$email, time(), $userId]
);

// Delete
$db->Execute(
    "DELETE FROM sessions WHERE user_id = ?",
    [$userId]
);
```

### MySQLTable Security

The `MySQLTable` class automatically validates:

**Column names:**
```php
// Pattern: /^[a-zA-Z_][a-zA-Z0-9_]*$/
// Allowed: id, user_name, firstName, _internal
// Blocked: id; DROP TABLE, user OR 1=1
```

**Table names:**
```php
// Pattern: /^[a-zA-Z_][a-zA-Z0-9_]*(?:\.[a-zA-Z_][a-zA-Z0-9_]*)?$/
// Allowed: users, my_table, schema.users
// Blocked: users; DROP TABLE, (SELECT *)
```

**Example:**
```php
$table = new MySQLTable();
$table->Database = $db;
$table->TableName = 'users';  // Validated
$table->Open();

// Values are inserted via prepared statements
$table->Fields['name'] = $userInput;  // Safe
$table->Fields['email'] = $email;     // Safe
$table->Post();
```

### ORDER BY Security

```php
// Order direction is validated
$table->OrderField = 'created_at';  // Column name validated
$table->Order = 'DESC';              // Only 'asc' or 'desc' allowed
```

---

## CSRF Protection

VCL's htmx integration uses CSRF tokens automatically.

### Automatic Protection

When htmx is enabled, a CSRF token is generated:

```php
$page->UseHtmx = true;
// Token is automatically stored in session and validated
```

### Manual CSRF Protection

```php
// Generate token
$token = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $token;

// Embed in form
echo '<input type="hidden" name="_csrf" value="' . Escaper::attr($token) . '">';

// Validate on submit
if (!hash_equals($_SESSION['csrf_token'], $_POST['_csrf'] ?? '')) {
    throw new SecurityException('CSRF token mismatch');
}
```

---

## Best Practices

### 1. Defense in Depth

Combine multiple security layers:

```php
// Input: Validate
$email = $validator->validateEmail($_POST['email']);

// Processing: Prepared statements
$db->Execute("INSERT INTO users (email) VALUES (?)", [$email]);

// Output: Escape
echo '<span>' . Escaper::html($email) . '</span>';
```

### 2. Context-Specific Escaping

Use the correct escape method for each context:

```php
// HTML text
<p><?= Escaper::html($name) ?></p>

// HTML attribute
<input value="<?= Escaper::attr($value) ?>">

// JavaScript
<script>var data = <?= Escaper::js($config) ?>;</script>

// URL
<a href="<?= Escaper::urlAttr($link) ?>">

// CSS
<div style="color: <?= Escaper::css($color) ?>">
```

### 3. Whitelist over Blacklist

```php
// BAD: Blacklist
$forbidden = ['<script>', 'javascript:', 'onerror'];
foreach ($forbidden as $bad) {
    $input = str_replace($bad, '', $input);
}

// GOOD: Whitelist
$validator->validateControlName($input);  // Only allowed characters
```

### 4. Fail Secure

```php
// On validation errors: Safe default
$url = Escaper::urlAttr($userUrl, '/default-page');

// On errors: Exception instead of silent failure
try {
    $id = $validator->validateInteger($input, min: 1);
} catch (SecurityException $e) {
    // Log and inform user
    error_log("Invalid input: " . $e->getMessage());
    http_response_code(400);
    exit('Invalid request');
}
```

### 5. Least Privilege for HTML

```php
// Allow only as much HTML as necessary
$comment = $sanitizer->sanitizeRichText($input);  // For comments
$article = $sanitizer->sanitizeFull($input);      // For CMS articles
$plain = $sanitizer->strip($input);               // For names, etc.
```

### 6. Safe Defaults

```php
// Escaper returns safe defaults
Escaper::urlAttr('javascript:alert(1)');  // Returns: '#'
Escaper::css('expression(evil)');          // Returns: ''
```

### 7. Limit Input Lengths

```php
// Always set length limits
$name = $validator->validateString($input, maxLength: 100);
$bio = $validator->validateString($input, maxLength: 5000);
```

---

## Additional Resources

- [OWASP XSS Prevention Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/Cross_Site_Scripting_Prevention_Cheat_Sheet.html)
- [OWASP SQL Injection Prevention](https://cheatsheetseries.owasp.org/cheatsheets/SQL_Injection_Prevention_Cheat_Sheet.html)
- [Symfony HtmlSanitizer](https://symfony.com/doc/current/html_sanitizer.html)
- [Symfony Validator](https://symfony.com/doc/current/validation.html)
