# HiddenField

Represents an HTML hidden input field.

**Namespace:** `VCL\Forms`
**File:** `src/VCL/Forms/HiddenField.php`
**Extends:** `Control`

## Usage

```php
use VCL\Forms\HiddenField;

$hidden = new HiddenField($this);
$hidden->Name = "HiddenField1";
$hidden->Parent = $this;
$hidden->Value = "secret_data";
```

## Properties

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `Value` | `string` | `''` | The hidden value |

## Events

| Property | Type | Description |
|----------|------|-------------|
| `OnSubmit` | `?string` | Called when form is submitted |

## Reading the Value

```php
public function SubmitClick(object $sender, array $params): void
{
    $value = $this->HiddenField1->Value;
    // Process hidden value...
}
```

## Example: Passing State

```php
class MyPage extends Page
{
    public ?HiddenField $UserIdField = null;

    public function __construct(?object $aowner = null)
    {
        parent::__construct($aowner);

        $this->UserIdField = new HiddenField($this);
        $this->UserIdField->Name = "UserIdField";
        $this->UserIdField->Parent = $this;
        $this->UserIdField->Value = "123";
    }

    public function SaveClick(object $sender, array $params): void
    {
        $userId = $this->UserIdField->Value;
        // Save with user ID...
    }
}
```

## Example: With OnSubmit Event

```php
$this->TokenField = new HiddenField($this);
$this->TokenField->Name = "TokenField";
$this->TokenField->Parent = $this;
$this->TokenField->Value = bin2hex(random_bytes(16));
$this->TokenField->OnSubmit = "TokenFieldSubmit";

public function TokenFieldSubmit(object $sender, array $params): void
{
    // Validate token
    $submitted = $this->TokenField->Value;
    // ...
}
```

## Generated HTML

```html
<input type="hidden" id="UserIdField" name="UserIdField" value="123" />
```

## Notes

- Value is automatically read from POST on preinit()
- Use for CSRF tokens, record IDs, state data
- Not visible to user but visible in page source
- Value persists across form submissions
